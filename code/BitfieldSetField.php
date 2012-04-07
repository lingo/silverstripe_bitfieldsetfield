<?php
/**
 * Displays a set of checkboxes as a logical group.
 * The source is defined as an array containing bitvalue => label
 * Example:
 * 	array(
 * 		1	=> 'First value',
 * 		2	=> 'Other value',
 * 		4	=> 'Another value'
 * 	);
 *
 * <b>Saving</b>
 * The checkbox set field will save its data by ORing the chosen values and saving into an Int field
 *
 * @author Luke Hudson <lukeletters@gmail.com>
 * @see
 */
class BitfieldSetField extends CheckboxSetField {

	function Field() {
		Requirements::css(SAPPHIRE_DIR . '/css/CheckboxSetField.css');

		$odd = 0;
		$options = new DataObjectSet();
		$source = $this->source;

		if($source) foreach($source as $value => $label) {
			$odd = ($odd + 1) % 2;
			$extraClass = $odd ? 'odd' : 'even';
			$extraClass .= ' val' . str_replace(' ', '', $value);
			$itemID = $this->id() . '_' . ereg_replace('[^a-zA-Z0-9]+', '', $value);
			$checked = '';

			$checked = ($this->value & $value) ? ' checked="checked"' : '';

			$disabled = ($this->disabled || in_array($value, $this->disabledItems)) ? $disabled = ' disabled="disabled"' : '';

			$options->push(new ArrayData(array(
				'ExtraClass'	=> $extraClass,
				'ItemID'		=> $itemID,
				'Checked'		=> $checked,
				'Disabled'		=> $disabled,
				'Name'			=> "$this->name[$value]",
				'Label'			=> $label,
				'Value'			=> $value
			)));
		}

		$data = new ArrayData(array(
			'Options'	=>	$options,
			'ID'		=>	$this->id(),
			'ExtraClass'=>	$this->extraClass()
		));
		return $this->customise($data)->renderWith(array('BitfieldSetField'));
		//return "<ul id=\"{$this->id()}\" class=\"optionset checkboxsetfield{$this->extraClass()}\">\n$options</ul>\n";
	}


	/**
	 * Load a value into this CheckboxSetField
	 */
	function setValue($value, $obj = null) {
		$this->value = 0;
		if (is_array($value)) foreach($value as $bit => $_) {
			$this->value |= $bit;
		} elseif(is_numeric($value)) {
			$this->value = $value;
		}
	}

	/**
	 * Save the current value of this CheckboxSetField into a DataObject.
	 * If the field it is saving to is a has_many or many_many relationship,
	 * it is saved by setByIDList(), otherwise it creates a comma separated
	 * list for a standard DB text/varchar field.
	 *
	 * @param DataObject $record The record to save into
	 */
	function saveInto(DataObject $record) {
		$fieldname = $this->name;
		$record->$fieldname = $this->value;
	}

	/**
	 * Return the CheckboxSetField value as a string
	 * selected item keys.
	 *
	 * @return string
	 */
	function dataValue() {
		if($this->value && is_array($this->value)) {
			$filtered = array();
			foreach($this->value as $item) {
				if($item) {
					$filtered[] = str_replace(",", "{comma}", $item);
				}
			}

			return implode(',', $filtered);
		}

		return '';
	}

	function performDisabledTransformation() {
		$clone = clone $this;
		$clone->setDisabled(true);

		return $clone;
	}

	/**
	 * Transforms the source data for this CheckboxSetField
	 * into a comma separated list of values.
	 *
	 * @return ReadonlyField
	 */
	function performReadonlyTransformation() {
		$out = array();
		foreach($this->source as $bit => $label) {
			if ($this->value & $bit) {
				$out[] = $label;
			}
		}
		$title = ($this->title) ? $this->title : '';

		$field = new BitfieldReadonlyField($this->name, $title);
		$field->setSource($this->source);
		return $field;
	}

	function ExtraOptions() {
		return FormField::ExtraOptions();
	}
}

/**
 * A read-only version of the field which shows only the set values, displayed by label
 */
class BitfieldReadonlyField extends ReadonlyField {

	protected $source = null;

	public function setSource(array $src) {
		$this->source = $src;
	}

	public function setValue($val) {
		if (is_numeric($val)) {
			$out = array();
			if ($this->source) foreach($this->source as $bit => $label) {
				if ($val & $bit) {
					$out[] = $label;
				}
			}
			$this->value = implode(', ', $out);
			return;
		}
		$this->value = $val;
	}
}
?>
