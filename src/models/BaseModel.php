<?php
namespace Blocks;

/**
 * Model base class
 *
 * @abstract
 */
abstract class BaseModel extends \CFormModel
{
	private $_properties = array();

	/**
	 * Returns a list of this model's properties.
	 *
	 * @return array
	 */
	protected function getProperties()
	{
		return array();
	}

	/**
	 * Isset?
	 *
	 * @param string $name
	 * @return bool
	 */
	function __isset($name)
	{
		return array_key_exists($name, $this->getProperties());
	}

	/**
	 * Attribute Setter
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @return mixed|void
	 * @throws Exception
	 */
	function __set($name, $value)
	{
		if (array_key_exists($name, $this->getProperties()))
			$this->_properties[$name] = $value;
		else
			$this->_noPropertyExists($name);
	}

	/**
	 * Attribute Getter
	 *
	 * @param string $name
	 * @throws Exception
	 * @return mixed|null
	 */
	function __get($name)
	{
		if (array_key_exists($name, $this->getProperties()))
		{
			if (isset($this->_properties[$name]))
				return $this->_properties[$name];
			else
				return null;
		}
		else if ($name == 'errors')
			return $this->getErrors();
		else
			$this->_noPropertyExists($name);
	}

	/**
	 * Throws a "no property exists" exception
	 *
	 * @param string $property
	 * @throws Exception
	 */
	private function _noPropertyExists($property)
	{
		throw new Exception(Blocks::t('“{class}” doesn’t have a property “{property}”.', array('class' => get_class($this), 'property' => $property)));
	}

	/**
	 * Used by CActiveRecord
	 *
	 * @return array Validation rules for model's properties
	 */
	public function rules()
	{
		return ModelHelper::createRules($this->getProperties());
	}
}
