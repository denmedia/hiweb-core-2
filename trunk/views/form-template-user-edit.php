<?php /** @var hw_form $this */ ?>
<table class="form-table">
	<tbody>
	<?php foreach($this->get_fields() as $field){ ?>
	<tr>
		<th><?php echo $field->name() ?></th>
		<td>
			<?php $field->the(); ?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>