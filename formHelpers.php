<?php
function validationAlert($message)
{
    return '<div class="alert alert-danger" role="alert">' . $message . '</div>';
}

function validateWithMessage($toValidateString, $regex, $message, &$flag)
{
    try {
        return validate($toValidateString, $regex, $message);
    } catch (UnexpectedValueException $exception) {
        $flag = false;
        echo validationAlert($exception->getMessage());
        return '';
    }
}

function validate($toValidateString, $regex, $message)
{
    $trimmed = trim($toValidateString);
    if (preg_match($regex, $trimmed)) {
        return htmlspecialchars($trimmed);
    };
    throw new UnexpectedValueException($message);
}
