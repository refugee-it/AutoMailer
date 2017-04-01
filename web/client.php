<?php
/* Copyright (C) 2017  Stephan Kreutzer
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
 * @file $/web/client.php
 * @brief Delivers the client to the user.
 * @author Stephan Kreutzer
 * @since 2017-03-27
 */



session_start();

if (isset($_SESSION['user_id']) !== true)
{
    header("HTTP/1.1 403 Forbidden");
    exit(-1);
}

header("Content-Type: application/xhtml+xml; charset=utf-8");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n".
     "<html version=\"-//W3C//DTD XHTML 1.1//EN\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.w3.org/1999/xhtml http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd\" xml:lang=\"en\" lang=\"en\">\n".
     "  <head>\n".
     "    <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    <!--\n".
     "    Copyright (C) 2017 Stephan Kreutzer\n".
     "\n".
     "    This program is free software: you can redistribute it and/or modify\n".
     "    it under the terms of the GNU Affero General Public License version 3 or any later version,\n".
     "    as published by the Free Software Foundation.\n".
     "\n".
     "    This program is distributed in the hope that it will be useful,\n".
     "    but WITHOUT ANY WARRANTY; without even the implied warranty of\n".
     "    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the\n".
     "    GNU Affero General Public License 3 for more details.\n".
     "\n".
     "    You should have received a copy of the GNU Affero General Public License 3\n".
     "    along with this program. If not, see &lt;http://www.gnu.org/licenses/&gt;.\n".
     "    -->\n".
     "    <title>AutoMailer Client</title>\n".
     "    <script type=\"application/javascript\" src=\"./client_script.php\"></script>\n".
     "  </head>\n".
     "  <body onload=\"send();\">\n".
     "    <h1>AutoMailer</h1>\n".
     "    <div id=\"result\"></div>\n".
     "    <a href=\"index.php\">Cancel</a>\n".
     "  </body>\n".
     "</html>\n";


?>
