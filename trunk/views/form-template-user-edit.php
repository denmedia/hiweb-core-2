<?php /** @var hw_form $this */ ?>
<table class="form-table">
	<tbody>
	<?php foreach( $this->get_fields() as $field ){ ?>
		<tr class="hw-field-<?php echo $field->type() ?>">
			<th><?php echo $field->label() ?></th>
			<td>
				<?php $field->the(); ?>
				<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>