<ul id="$ID" class="optionset checkboxsetfield bitfieldsetfield $ExtraClass">
	<% if Options.TotalItems %>
		<% control Options %>
			<li class="$ExtraClass">
				<input id="$ItemID" name="$Name" type="checkbox" value="$Value" $Checked $Disabled class="checkbox" />
				<label for="$ItemID">$Label</label>
			</li>
		<% end_control %>
	<% else %>
		<li>No options available</li>	
	<% end_if %>
</ul>
