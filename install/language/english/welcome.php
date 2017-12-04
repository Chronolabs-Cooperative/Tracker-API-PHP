<?php
//
// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: API Translation Team

$content .= '
<p>
    <abbr title="REST API">API</abbr> is an open-source
    api that is application programable interface that requires installation and configuration for the API
    to work and function within the normal operating environmentals!
</p>
<p>
    API is released under the terms of the
    <a href="http://www.gnu.org/licenses/gpl-2.0.html" rel="external">GNU General Public License (GPL)</a>
    version 2 or greater, and is free to use and modify.
    It is free to redistribute as long as you abide by the distribution terms of the GPL.
</p>
<h3>Requirements</h3>
<ul>
    <li>WWW Server (<a href="http://www.apache.org/" rel="external">Apache</a>, <a href="https://www.nginx.com/" rel="external">NGINX</a>, IIS, etc)</li>
    <li><a href="http://www.php.net/" rel="external">PHP</a> 5.3.7 or higher, 5.6+ recommended</li>
    <li><a href="http://www.mysql.com/" rel="external">MySQL</a> 5.5 or higher, 5.6+ recommended </li>
</ul>
<h3>Before you install</h3>
<ol>
    <li>Setup WWW server, PHP and tmpbase server properly.</li>
    <li>Prepare a tmpbase for your API site.</li>
    <li>Prepare user account and grant the user the access to the tmpbase.</li>
    <li>Make these directories and files writable: %s</li>
    <li>For security considerations, you are strongly advised to move the two directories below out of <a href="http://phpsec.org/projects/guide/3.html" rel="external">document root</a> and change the folder names: %s</li>
    <li>Create (if not already present) and make these directories writable: %s</li>
    <li>Turn cookie and JavaScript of your browser on.</li>
</ol>
';
