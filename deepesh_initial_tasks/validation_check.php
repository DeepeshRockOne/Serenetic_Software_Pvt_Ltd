<?php
    include_once dirname(__DIR__) ."/deepesh_initial_tasks/registration_using_pdo/includes/Validation.php";

    $first_name = "";
    $last_name = "Hello Games Name James";
    $email = "tdfsdsds@gmail.org";
    $date_of_birth = "1994-10-25";
    $phone = 7788994455;
    $digit = "5552";
    $price = 500;
    $url = "https://google.co.in";

    $validation = new Validation();

    $validation->string(array('field'=>'first_name', 'value'=>$first_name), array('required'=>'First name is required.'));

    $validation->string(array('field'=>'last_name', 'value'=>$last_name, 'min'=>10, 'max'=>15), array('required'=>'Last name is required.'));

    $a = 5;
    if ($a > 5) {
        $validation->setError('last_name', 'Last name is required cusotm message.');
    }

    $validation->email(array('required' => true, 'field'=>'email', 'value'=>$email, 'min'=>15), array('required'=>'Email is required.', 'invalid'=>'Please Enter valid email.', 'min'=>'Minimum 15 character is required for Email.'));

    $validation->validDate(array('required' => true, 'field'=>'date_of_birth', 'value'=>$date_of_birth), array('required'=>'Date of birth is required.', 'invalid'=>'Please Enter valid date of birth.'));

    $validation->phone(array('required' => true, 'field'=>'phone', 'value'=>$phone), array('required'=>'Phone number is required.', 'invalid'=>'Please Enter valid phone number.'));

    $validation->digit(array('required' => true, 'field'=>'digit', 'value'=>$digit), array('required'=>'Digit is required.', 'invalid'=>'Digits only.',));

    $validation->amount(array('required' => true, 'field'=>'price', 'value'=>$price), array('required'=>'Amount is required.', 'invalid'=>'Please enter valid price.',));

    //$validation->url(array('required' => true, 'field'=>'url', 'value'=>$url), array('required'=>'URL is required.', 'invalid'=>'Please enter valid URL.',));

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['image_submit'])) {
            $image_name = $_FILES['image']['name'];
            $image_size = $_FILES['image']['size'];
            $image_type = $_FILES['image']['type'];

            $validation->image(array('required' => true, 'field'=>'image', 'value'=>array('name'=>$image_name, 'type'=>$image_type, 'size'=>$image_size)), array('required'=>'Image field is required.'));
        }
    }

    //get all the errors
    echo "<pre>"; print_r($validation->getErrors()); echo "<br/>";

    //get specific field vise errors
    echo $validation->getError('first_name') . "<br/>";

    echo $validation->getError('last_name') . "<br/>";
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image">
    <input type="submit" name="image_submit" value="Upload">
</form>