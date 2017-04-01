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
 * @file $/web/client_script.php
 * @brief Delivers the JavaScript part of the client to the user.
 * @author Stephan Kreutzer
 * @since 2017-03-27
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
    define("CORS_HOST", "https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/automailer.php");
}
else
{
    define("CORS_HOST", "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/automailer.php");
}



header("Content-Type: application/javascript");

echo
"/* Copyright (C) 2012-2017 Stephan Kreutzer\n".
" *\n".
" * This file is part of AutoMailer by refugee-it.de.\n".
" *\n".
" * AutoMailer is free software: you can redistribute it and/or modify\n".
" * it under the terms of the GNU Affero General Public License version 3 or any later version,\n".
" * as published by the Free Software Foundation.\n".
" *\n".
" * AutoMailer is distributed in the hope that it will be useful,\n".
" * but WITHOUT ANY WARRANTY; without even the implied warranty of\n".
" * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the\n".
" * GNU Affero General Public License 3 for more details.\n".
" *\n".
" * You should have received a copy of the GNU Affero General Public License 3\n".
" * along with AutoMailer. If not, see <http://www.gnu.org/licenses/>.\n".
" */\n".
"\n".
"var xmlhttp = null;\n".
"\n".
"// Mozilla\n".
"if (window.XMLHttpRequest)\n".
"{\n".
"    xmlhttp = new XMLHttpRequest();\n".
"}\n".
"// IE\n".
"else if (window.ActiveXObject)\n".
"{\n".
"    xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');\n".
"}\n".
"\n".
"function send()\n".
"{\n".
"    if (xmlhttp == null)\n".
"    {\n".
"        alert('No HTTP request object.');\n".
"        return;\n".        
"    }\n".
"\n".
"    xmlhttp.open('POST', '".CORS_HOST."', true);\n".
"    xmlhttp.setRequestHeader('Content-Type',\n".
"                             'application/x-www-form-urlencoded');\n".
"    xmlhttp.onreadystatechange = response;\n".
"    xmlhttp.send();\n".
"}\n".
"\n".
"function response()\n".
"{\n".
"    if (xmlhttp.readyState != 4)\n".
"    {\n".
"        // Waiting...\n".
"    }\n".
"\n".
"    var resultTarget = document.getElementById('result');\n".
"\n".
"    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)\n".
"    {\n".
"        if (xmlhttp.responseText == '')\n".
"        {\n".
"            alert('Empty response.');\n".
"            return;\n".
"        }\n".
"\n".
"        var dom = xmlhttp.responseXML.documentElement;\n".
"        var result = dom.getElementsByTagName('result').item(0).firstChild.data;\n".
"\n".
"            var resultDiv = document.createElement('div');\n".
"            var resultP = document.createElement('p');\n".
"\n".
"        if (result == 'success')\n".
"        {\n".
"            var milliseconds = Math.floor((Math.random() * 65000) + 5000);\n".
"\n".
"            var resultText = document.createTextNode('Sent. Next in ' + (milliseconds / 1000) + ' seconds.');\n".
"\n".
"            resultP.appendChild(resultText);\n".
"            resultDiv.appendChild(resultP);\n".
"            resultTarget.appendChild(resultDiv);\n".
"\n".
"            window.setTimeout(send, milliseconds);\n".
"        }\n".
"        else if (result == 'failure')\n".
"        {\n".
"            var resultText = document.createTextNode('Failed.');\n".
"\n".
"            resultP.appendChild(resultText);\n".
"            resultDiv.appendChild(resultP);\n".
"            resultTarget.appendChild(resultDiv);\n".
"\n".
"            return;\n".
"        }\n".
"        else if (result == 'end')\n".
"        {\n".
"            var resultText = document.createTextNode('End.');\n".
"\n".
"            resultP.appendChild(resultText);\n".
"            resultDiv.appendChild(resultP);\n".
"            resultTarget.appendChild(resultDiv);\n".
"\n".
"            return;\n".
"        }\n".
"        else\n".
"        {\n".
"            var resultText = document.createTextNode('Unknown status.');\n".
"\n".
"            resultP.appendChild(resultText);\n".
"            resultDiv.appendChild(resultP);\n".
"            resultTarget.appendChild(resultDiv);\n".
"\n".
"            return;\n".
"        }\n".
"    }\n".
"    else if (xmlhttp.readyState == 4 && xmlhttp.status == 0)\n".
"    {\n".
"        var resultText = document.createTextNode('Offline.');\n".
"\n".
"        resultP.appendChild(resultText);\n".
"        resultDiv.appendChild(resultP);\n".
"        resultTarget.appendChild(resultDiv);\n".
"\n".
"        return;\n".
"    }\n".
"}\n".
"\n";

?>
