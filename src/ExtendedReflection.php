<?php
namespace Masunov\LaravelDtoYmlGenerator;

use \ReflectionClass;
use \RuntimeException;

class ExtendedReflection extends ReflectionClass {

    /**
     * @var array
     */
	protected $useStatements = [];

    /**
     * @var bool
     */
	protected $useStatementsParsed = false;

    /**
     * @return array
     */
	protected function parseUseStatements(): array {
		if ($this->useStatementsParsed) {
			return $this->useStatements;
		}
		if (!$this->isUserDefined()) {
			throw new RuntimeException('Must parse use statements from user defined classes.');
		}
		$source = $this->readFileSource();
		$this->useStatements = $this->tokenizeSource($source);
		$this->useStatementsParsed = true;
		return $this->useStatements;
	}

    /**
     * @return string
     */
	private function readFileSource() {

		$file = fopen($this->getFileName(), 'r');
		$line = 0;
		$source = '';
		while (!feof($file)) {
			++$line;
			if ($line >= $this->getStartLine()) {
				break;
			}
			$source .= fgets($file);
		}
		fclose($file);

		return $source;
	}

    /**
     * @param $source
     * @return array
     */
	private function tokenizeSource($source) {
		$tokens = token_get_all($source);
		$builtNamespace = '';
		$buildingNamespace = false;
		$matchedNamespace = false;
		$useStatements = [];
		$record = false;
		$currentUse = [
			'class' => '',
			'as' => ''
		];
		foreach ($tokens as $token) {
			if ($token[0] === T_NAMESPACE) {
				$buildingNamespace = true;
				if ($matchedNamespace) {
					break;
				}
			}
			if ($buildingNamespace) {
				if ($token === ';') {
					$buildingNamespace = false;
					continue;
				}
				switch ($token[0]) {
					case T_STRING:
					case T_NS_SEPARATOR:
						$builtNamespace .= $token[1];
						break;
				}
				continue;
			}
			if ($token === ';' || !is_array($token)) {
				if ($record) {
					$useStatements[] = $currentUse;
					$record = false;
					$currentUse = [
						'class' => '',
						'as' => ''
					];
				}
				continue;
			}
			if ($token[0] === T_CLASS) {
				break;
			}
			if (strcasecmp($builtNamespace, $this->getNamespaceName()) === 0) {
				$matchedNamespace = true;
			}
			if ($matchedNamespace) {
				if ($token[0] === T_USE) {
					$record = 'class';
				}
				if ($token[0] === T_AS) {
					$record = 'as';
				}
				if ($record) {
					switch ($token[0]) {
						case T_STRING:
						case T_NS_SEPARATOR:
							if ($record) {
								$currentUse[$record] .= $token[1];
							}
							break;
					}
				}
			}
			if ($token[2] >= $this->getStartLine()) {
				break;
			}
		}

		foreach ($useStatements as &$useStatement) {
			if (empty($useStatement['as'])) {

				$useStatement['as'] = basename($useStatement['class']);
			}
		}
		return $useStatements;
	}

    /**
     * @return array
     */
	public function getUseStatements() {
		return $this->parseUseStatements();
	}

    /**
     * @param $class
     * @return bool
     */
	public function getUseStatement($class) {
        $useStatements = $this->parseUseStatements();
        return
            array_search($class, array_column($useStatements, 'class')) ||
            array_search($class, array_column($useStatements, 'as'));
    }

    /**
     * @param $class
     * @return bool
     */
	public function hasUseStatement($class) {
		$useStatements = $this->parseUseStatements();
		return
			array_search($class, array_column($useStatements, 'class')) ||
			array_search($class, array_column($useStatements, 'as'));
	}
}