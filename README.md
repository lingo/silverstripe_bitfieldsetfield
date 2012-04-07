BitfieldSetField
================

**Author:** Luke Hudson <lukeletters@gmail.com>

This is essentially a CheckboxSetField but it reads and writes to an Int field, ORing the values chosen.
To use it, provide it with a source array in the constructor, which maps bit values to labels.

Example
-------

	// In your DB definition, create an Int field
	static $db = array(
		. . .
		'Format'	=> 	'Int'
	);

	. . .

	// in getCMSFields, initialise the control, providing bit=>label mapping.
	// Remember, the array indices represent bit values, so must be powers of two!
	function getCMSFields() {
		. . .
		$formats = array(
			1	=>  'Print',
			2	=>	'Online',
			4	=>	'Email'
		);
		$fields->push( new BitfieldSetField('Format', null, $formats) );


