<?php
/* Copyright (C) 2012-2017 Stephan Kreutzer
 *
 * This file is part of AutoMailer by refugee-it.de.
 *
 * AutoMailer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3 or any later version,
 * as published by the Free Software Foundation.
 *
 * AutoMailer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License 3 for more details.
 *
 * You should have received a copy of the GNU Affero General Public License 3
 * along with AutoMailer. If not, see <http://www.gnu.org/licenses/>.
 */


session_start();

if (isset($_SESSION['user_id']) !== true)
{
    header("HTTP/1.1 403 Forbidden");
    exit(-1);
}


define("HTTPS_ENABLED", false);

if (isset($_SERVER['HTTPS']) === true)
{
    if ($_SERVER['HTTPS'] === "on")
    {
        define("HTTPS_ENABLED", true);
    }
}

if (HTTPS_ENABLED === true)
{
    define("HOST_PATH", "https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']));
}
else
{
    define("HOST_PATH", "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']));
}


$success = true;

require_once("./libraries/database.inc.php");

if (Database::Get()->IsConnected() !== true)
{
    $success = false;
}

if ($success === true)
{
    $addressee = Database::Get()->QueryUnsecure("SELECT `id`,\n".
                                                "    `family_name`,\n".
                                                "    `given_name`,\n".
                                                "    `gender`,\n".
                                                "    `e_mail`\n".
                                                "FROM `".Database::Get()->GetPrefix()."addressees`\n".
                                                "WHERE `sent` IS NULL\n".
                                                "LIMIT 1");

    if (is_array($addressee) === true)
    {
        if (count($addressee) > 0)
        {
            $addressee = $addressee[0];
        }
        else
        {
            $success = false;
        }
    }
    else
    {
        $success = false;
    }
}

$inTransaction = false;

if ($success === true)
{
    if (Database::Get()->BeginTransaction() === true)
    {
        $inTransaction = true;
    }
    else
    {
        $success = false;
    }
}

if ($success === true)
{
    if (Database::Get()->ExecuteUnsecure("UPDATE `".Database::Get()->GetPrefix()."addressees`\n".
                                         "SET `sent`=NOW()\n".
                                         "WHERE `id`=".((int)$addressee['id'])."\n") !== true)
    {
        $success = false;
    }
}

$errorMessage = "";

if ($success === true)
{
    $message = "Dear ";

    if ((int)$addressee['gender'] === 1)
    {
        $message .= "Mr. ";

        if (!empty($addressee['given_name']))
        {
            $message .= $addressee['given_name']." ";
        }

        $message .= $addressee['family_name'];
    }
    else if ((int)$addressee['gender'] === 2)
    {
        $message .= "Ms. ";

        if (!empty($addressee['given_name']))
        {
            $message .= $addressee['given_name']." ";
        }

        $message .= $addressee['family_name'];
    }
    else
    {
        $message .= "Sir or Madam";
    }

    $message .= ",\n".
                "\n".
                "if you've received this e-mail without knowing why, you might want to complain to the webmaster of\n".
                "\n".
                "    ".HOST_PATH."\n".
                "\n".
                "\n".
                "Sincerely,\n".
                "AutoMailer\n";


    require_once("./libraries/PHPMailer/PHPMailerAutoload.php");

    $mailer = new PHPMailer();
    $mailer->setFrom("no-reply@example.org", "NoReply");
    $mailer->addAddress($addressee['e_mail']);

    $mailer->CharSet = "utf-8";
    $mailer->Encoding = "base64";
    $mailer->Subject = "Message Subject";
    $mailer->isHTML(false);
    $mailer->Body = $message;

    $mailer->AddAttachment("full-path", "display-name");

    if ($mailer->Send() !== true)
    {
        $success = false;

        $errorMessage = $mailer->ErrorInfo;
    }
}

if ($success === true)
{
    if (Database::Get()->CommitTransaction() !== true)
    {
        $success = false;
    }
}
else
{
    if ($inTransaction === true)
    {
        Database::Get()->RollbackTransaction();
    }
}

header("Content-Type: text/xml");
//header("Access-Control-Allow-Origin: *");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>".
     "<!-- Generated by AutoMailer, which is free software licensed under the GNU Affero General Public License 3 or any later version (see https://github.com/refugee-it/AutoMailer/ and https://www.refugee-it.de). -->".
     "<response><result>";

if ($success === true)
{
    echo "success";
}
else
{
    if (count($addressee) <= 0)
    {
        echo "end";
    }
    else
    {
        echo "failure";
    }
}

echo "</result>";

if (!empty($errorMessage))
{
    echo "<error>".htmlspecialchars($errorMessage, ENT_XML1, "UTF-8")."</error>";
}

echo "</response>";


?>