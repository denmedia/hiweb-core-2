<?php /** @var hw_form $this */ ?>
<table class="form-table">
	<tbody>
	<?php foreach( $this->get_fields() as $field ){ ?>
		<tr class="hw-field-<?= $field->type() ?>" data-field-id="<?= $field->id() ?>" data-field-global-id="<?= $field->global_id() ?>">
			<th><?php echo $field->label() ?></th>
			<td>
				<?php $field->the(); ?>
				<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>