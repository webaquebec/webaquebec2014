<fieldset id="conference_options">
	<table>
		<?php
			Conferencer::add_meta($post);
			$user_option_count = 0;
		?>
		<?php foreach($this->options as $name => $option) { ?>
			<?php
				if ($option['type'] == 'internal') continue;
				$user_option_count++;
				$value = isset($$name) ? $$name : $post->$name;
				$name = "conferencer_$name";
			?>
			
			<tr>
				<td class="label">
					<label for="<?php echo $name; ?>">
						<?php echo $option['label']; ?>
					</label>
				</td>
				
				<td class="input">
					<?php if ($option['type'] == 'text') { ?>
						<input
							class="text"
							type="text"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							value="<?php echo htmlentities($value,ENT_COMPAT,'UTF-8'); ?>"
						/>
					<?php } else if ($option['type'] == 'int') { ?>
						<input
							class="int"
							type="text"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							value="<?php echo htmlentities($value); ?>"
						/>
					<?php } else if ($option['type'] == 'date-time') { ?>
						<input
							class="date"
							type="text"
							name="<?php echo $name; ?>[date]"
							id="<?php echo $name.'_date'; ?>"
							value="<?php if ($value) echo date('n/j/Y', $value); ?>"
							placeholder="mm/dd/yyyy"
							<?php if ($this->earliest_time_slot_date) { // TODO: test this on first time slot ?>
								default="<?php echo date('n/j/Y', $this->earliest_time_slot_date); ?>"
							<?php } ?>
						/>
						<input
							class="time"
							type="text"
							name="<?php echo $name; ?>[time]"
							id="<?php echo $name.'_time'; ?>"
							value="<?php if ($value) echo date('g:ia', $value); ?>"
							placeholder="hh:mm am"
						/>
					<?php } else if ($option['type'] == 'money') { ?>
						<input
							class="money"
							type="text"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							value="<?php echo htmlentities($value); ?>"
						/>
					<?php } else if ($option['type'] == 'select') { ?>
						<select
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
						>
							<option value=""></option>
							<?php foreach ($option['options'] as $optionValue => $text) { ?>
								<option
									value="<?php echo $optionValue; ?>"
									<?php if ($optionValue == $value) echo 'selected'; ?>>
									<?php echo $text; ?>
								</option>
							<?php } ?>
						</select>
					<?php } else if ($option['type'] == 'multi-select') { ?>
						<?php
							$multivalues = $value;
							if (!$multivalues || !is_array($multivalues)) $multivalues = array(null);
						?>
						<ul>
							<?php foreach ($multivalues as $multivalue) {?>
								<li>
									<select name="<?php echo $name; ?>[]">
										<option value=""></option>
										<?php foreach ($option['options'] as $optionValue => $text) { ?>
											<option
												value="<?php echo $optionValue; ?>"
												<?php if ($optionValue == $multivalue) echo 'selected'; ?>
											>
												<?php echo $text; ?>
											</option>
										<?php } ?>
									</select>
								</li>
							<?php } ?>
						</ul>
						<a class="add-another" href="#">add another</a>
					<?php } else if ($option['type'] == 'boolean') { ?>
						<input
							type="checkbox"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							<?php if ($value) echo 'checked'; ?>
						/>
					<?php } else echo 'unknown option type'; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php if (!$user_option_count) { ?>
		<p>There aren't any Conferencer options for <?php echo $this->plural; ?>.</p>
	<?php } ?>
</fieldset>