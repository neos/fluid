<?php
declare(ENCODING = 'utf-8');
namespace F3::Beer3;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package Beer3
 * @version $Id:$
 */
/**
 * A node which handles object access.
 *
 * @package
 * @subpackage
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 * @scope prototype
 */
class ObjectAccessorNode extends F3::Beer3::AbstractNode {
	
	/**
	 * Object path which will be called.
	 * @var string
	 */
	protected $objectPath;
	
	/**
	 * Constructor. Takes an object path as input.
	 * 
	 * The first part of the object path has to be a variable in the context.
	 * For the further parts, it is checked if the object has a getObjectname method. If yes, this is called.
	 * If no, it is checked if a property "objectname" exists.
	 * If no, an error is thrown.
	 * 
	 * @param string $objectPath An Object Path, like object1.object2.object3
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function __construct($objectPath) {
		$this->objectPath = $objectPath;
	}
	
	/**
	 * Evaluate this node and return the correct object.
	 * 
	 * Handles each part (denoted by .) in $this->objectPath in the following order:
	 * - call appropriate getter
	 * - call public property, if exists
	 * - fail
	 * 
	 * @return object The evaluated object, can be a string...
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function evaluate(F3::Beer3::Context $context) {
		$objectPathParts = explode('.', $this->objectPath);
		$contextVariableName = array_shift($objectPathParts);
		$currentObject = $context->get($contextVariableName);
		
		if (count($objectPathParts) > 0) {
			foreach ($objectPathParts as $currentObjectPath) {
				if (is_object($currentObject)) {
					$getterMethodName = 'get' . F3::PHP6::Functions::ucfirst($currentObjectPath);
					if (method_exists($currentObject, $getterMethodName)) {
						$currentObject = call_user_func(array($currentObject, $getterMethodName));
						continue;
					}
					
					try {
						$reflectionProperty = new ReflectionProperty($currentObject, $currentObjectPath);
					} catch(ReflectionException $e) {
						throw new F3::Beer3::RuntimeException($e->getMessage(), 1224611407);
					}
					if ($reflectionProperty->isPublic()) {
						$currentObject = $reflectionProperty->getValue($currentObject);
						continue;
					} else {
						throw new F3::Beer3::RuntimeException('Trying to resolve ' . $this->objectPath . ', but did not find public getters or variables.', 1224609559);
					}
				} elseif (is_array($currentObject)) {
					if (key_exists($currentObjectPath, $currentObject)) {
						$currentObject = $currentObject[$currentObjectPath];
					} else {
						throw new F3::Beer3::RuntimeException('Tried to read key "' . $currentObjectPath . '" from associative array, but did not find it.', 1225393852);
					}
				}
			}
		}
		return $currentObject;
	}
}


?>