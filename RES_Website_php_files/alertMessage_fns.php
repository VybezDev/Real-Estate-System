<?php
function alertMessage($message)
{
	?>
	<script type='text/javascript'>
		window.onload = function () { alert("<?php echo $message; ?>"); }
	</script>
	<?php
}
?>