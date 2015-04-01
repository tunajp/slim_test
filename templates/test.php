<?php include('common/header.php') ?>

<div>
<?php echo $values['id'] ?>
</div>

<div>
<?php echo $values['name'] ?>
</div>

<form action="./post_test" method="post">
<input type="text" name="name">
<input type="text" name="age">
<input type="radio" name="sex" value="男">
<input type="radio" name="sex" value="女">
<input type="submit" value="Submit">

<input type="hidden" name="id" value="<?php echo $values['id']?>" >
</form>

<?php include('common/footer.php') ?>
