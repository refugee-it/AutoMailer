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


require_once("./libraries/database.inc.php");

$addressees = null;

if (Database::Get()->IsConnected() === true)
{
    $addressees = Database::Get()->QueryUnsecure("SELECT `id`,\n".
                                                "    `family_name`,\n".
                                                "    `given_name`,\n".
                                                "    `e_mail`,\n".
                                                "    `sent`\n".
                                                "FROM `".Database::Get()->GetPrefix()."addressees`\n".
                                                "WHERE 1\n");
}

header("Content-Type: application/xhtml+xml; charset=utf-8");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n".
     "<html version=\"-//W3C//DTD XHTML 1.1//EN\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.w3.org/1999/xhtml http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd\" xml:lang=\"en\" lang=\"en\">\n".
     "  <head>\n".
     "    <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    <title>AutoMailer Protocol</title>\n".
     "  </head>\n".
     "  <body>\n".
     "    <h1>Protocol</h1>\n".
     "    <table>\n".
     "      <thead>\n".
     "        <tr>\n".
     "          <th>Id</th>\n".
     "          <th>Family Name</th>\n".
     "          <th>Given Name</th>\n".
     "          <th>E-Mail</th>\n".
     "          <th>Sent</th>\n".
     "        </tr>\n".
     "      </thead>\n".
     "      <tbody>\n";

if (is_array($addressees))
{
    if (count($addressees) > 0)
    {
        foreach ($addressees as $addressee)
        {
            echo "        <tr>\n".
                 "          <td>".((int)$addressee['id'])."</td>\n".
                 "          <td>".htmlspecialchars($addressee['family_name'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</td>\n".
                 "          <td>".htmlspecialchars($addressee['given_name'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</td>\n".
                 "          <td>".htmlspecialchars($addressee['e_mail'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</td>\n".
                 "          <td>".htmlspecialchars($addressee['sent'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</td>\n".
                 "        </tr>\n";
        }
    }
}

echo "      </tbody>\n".
     "    </table>\n".
     "    <a href=\"index.php\">Leave</a>\n".
     "  </body>\n".
     "</html>\n";


?>