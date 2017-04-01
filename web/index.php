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
/**
 * @file $/web/index.php
 * @brief Start page.
 * @author Stephan Kreutzer
 * @since 2012-06-01
 */



if (empty($_SESSION) === true)
{
    @session_start();
}

if (isset($_POST['logout']) === true &&
    isset($_SESSION['user_id']) === true)
{
    $_SESSION = array();

    if (isset($_COOKIE[session_name()]) == true)
    {
        setcookie(session_name(), '', time()-42000, '/');
    }
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
     "  <head>\n".
     "      <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "      <title>AutoMailer</title>\n".
     "  </head>\n".
     "  <body>\n";

if (isset($_POST['name']) !== true ||
    isset($_POST['password']) !== true)
{
    echo "    <div>\n";

    if (file_exists("./install.php") === true &&
        isset($_GET['skipinstall']) != true)
    {
        echo "      <a href=\"install.php\">Install</a>\n";

        require_once("./license.inc.php");
        echo getHTMLLicenseNotification("license");
    }
    else
    {
        if (isset($_SESSION['user_id']) === true)
        {
            echo "      <p>\n".
                 "        <a href=\"client.php\">Client</a><br/>\n".
                 "        <a href=\"protocol.php\">Protocol</a><br/>\n".
                 "      </p>\n".
                 "      <form action=\"index.php\" method=\"post\">\n".
                 "        <fieldset>\n".
                 "          <input type=\"submit\" name=\"logout\" value=\"Logout\"/><br/>\n".
                 "        </fieldset>\n".
                 "      </form>\n";
        }
        else
        {
            echo "      <form action=\"index.php\" method=\"post\">\n".
                 "        <fieldset>\n".
                 "          <input name=\"name\" type=\"text\" size=\"20\" maxlength=\"60\"/> Name<br />\n".
                 "          <input name=\"password\" type=\"password\" size=\"20\" maxlength=\"60\"/> Password<br />\n".
                 "          <input type=\"submit\" value=\"Submit\"/><br/>\n".
                 "        </fieldset>\n".
                 "      </form>\n";

            require_once("./license.inc.php");
            echo getHTMLLicenseNotification("license");
        }
    }

    echo "    </div>\n".
         "  </body>\n".
         "</html>\n".
         "\n";
}
else
{
    echo "    <div>\n";

    require_once("./libraries/database.inc.php");

    if (Database::Get()->IsConnected() !== true)
    {
        echo "      <p>\n".
             "        Database not connected.\n".
             "      </p>\n".
             "    </div>\n".
             "  </body>\n".
             "</html>\n".
             "\n";

        exit(-1);
    }

    $user = Database::Get()->Query("SELECT `id`,\n".
                                   "    `salt`,\n".
                                   "    `password`\n".
                                   "FROM `".Database::Get()->GetPrefix()."users`\n".
                                   "WHERE `name` LIKE ?\n",
                                   array($_POST['name']),
                                   array(Database::TYPE_STRING));

    if (is_array($user) !== true)
    {
        echo "      <p>\n".
             "        Database query failed.\n".
             "      </p>\n".
             "    </div>\n".
             "  </body>\n".
             "</html>\n".
             "\n";

        exit(-1);
    }

    if (count($user) === 0)
    {
        echo "      <p>\n".
             "        Login failed. <a href=\"index.php\">Retry</a>\n".
             "      </p>\n".
             "    </div>\n".
             "  </body>\n".
             "</html>\n".
             "\n";

        exit(0);
    }
    else
    {
        // The user does exist, he wants to login.

        if ($user[0]['password'] === hash('sha512', $user[0]['salt'].$_POST['password']))
        {
            $_SESSION['user_id'] = (int)$user[0]['id'];
            $_SESSION['user_name'] = $_POST['name'];

            echo "      <p>\n".
                 "        Login successful. <a href=\"index.php\">Continue</a>\n".
                 "      </p>\n".
                 "    </div>\n".
                 "  </body>\n".
                 "</html>\n".
                 "\n";

            exit(0);
        }
        else
        {
            echo "      <p>\n".
                 "        Login failed. <a href=\"index.php\">Retry</a>\n".
                 "      </p>\n".
                 "    </div>\n".
                 "  </body>\n".
                 "</html>\n".
                 "\n";

            exit(0);
        }
    }

    echo "    </body>\n".
         "</html>\n";
}


?>
