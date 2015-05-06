<?php include('header.php'); ?>

<div id="body">
<h2>AMH » Logout</h2>

Please wait ...
<script>
var url = '<?php echo _Http;?>ams/index.php?c=index&a=logout';
Ajax.get(url, function ()
{
	window.location = '/index.php';
});
</script>
</div>
<?php include('footer.php'); ?>
