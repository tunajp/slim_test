<?php include('common/header.php') ?>

<?php use PhoenixDesign\Lib as plib; ?>

<div>
<?php echo plib\Util::h($values['id']) ?>
</div>

<div>
<?php echo plib\Util::h($values['name']) ?>
</div>

<form action="<?php echo \Config::$app_path ?>post_test/" method="post">
<input type="text" id="name" name="name">
<input type="text" id="age" name="age">
<input type="radio" id="man" name="sex" value="男">
<input type="radio" id="woman" name="sex" value="女">
<input type="submit" id="submit" value="Submit">

<input type="hidden" name="id" value="<?php echo $values['id']?>" >
</form>

<?php include('common/footer.php') ?>
