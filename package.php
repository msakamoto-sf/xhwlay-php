<?php
/*
 *   Copyright (c) 2007 msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.*
 */

/*
 * package.xml/package2.xml generator
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: package.php 59 2008-03-05 19:49:07Z msakamoto-sf $
 */

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

// {{{ Package Information Items

$releaseVersion = '0.9.3';
$releaseStability = 'beta';
$apiVersion = '0.9.3';
$apiStability = 'beta';
$summary = 'Event Driven Oriented Stateful Page Flow Execution Engine Library';
$description = 'Xhwlay provides event-driven oriented statefull page flow 
control library. Xhwlay has 3 main concepts :
1. Simple
2. Primitive
3. Small
These concept will lead ease to learn, ease to hack, ease to use, and 
more and more flexibility for developers.';
$notes = '
- Fix Xhwlay_Bookmark_FileStoreContainer\'s gc() bug and test case bugs.
';
$changelog = '
- Fix Xhwlay_Bookmark_FileStoreContainer\'s gc() bug and test case bugs.
';

// }}}

$pkg2xml = new PEAR_PackageFileManager2();
$pkg2xml->setOptions(array(
    'packagefile' => 'package2.xml',
    'filelistgenerator' => 'file',
    'packagedirectory' => dirname(__FILE__),
    'baseinstalldir' => '/',
    'ignore' => array(
        basename(__FILE__),
        dirname(__FILE__) . '/test_dirty',
        dirname(__FILE__) . '/test_dirty/*',
        dirname(__FILE__) . '/sample/datas/*',
        dirname(__FILE__) . '/sample/sess/*',
        dirname(__FILE__) . '/sample2/datas/*',
        dirname(__FILE__) . '/sample2/sess/*',
        ),
    'dir_roles' => array(
        'sample' => 'doc',
        'sample2' => 'doc',
        'docs' => 'doc',
        'test' => 'test',
        ),
    'exceptions' => array(
        'LICENSE' => 'doc',
        ),
    'changelogoldtonew' => true,
    'changelognotes' => $changelog,
//    'simpleoutput' => true,
    ));
$pkg2xml->setPackageType('php'); // this is a PEAR-style php script package
$pkg2xml->addRelease();
$pkg2xml->setPackage('Xhwlay');
$pkg2xml->setUri('http://xhwlay.sourceforge.net/');
$pkg2xml->setReleaseVersion($releaseVersion);
$pkg2xml->setAPIVersion($apiVersion);
$pkg2xml->setReleaseStability($releaseStability);
$pkg2xml->setAPIStability($apiStability);
$pkg2xml->setSummary($summary);
$pkg2xml->setDescription($description);
$pkg2xml->setNotes($notes);
$pkg2xml->setPhpDep('4.3.0'); // PHP 4.3.0 - 
$pkg2xml->addPackageDepWithChannel('required', 'File', 'pear.php.net');
$pkg2xml->addPackageDepWithChannel('required', 'PHP_Compat', 'pear.php.net');
$pkg2xml->addPackageDepWithChannel('optional', 'Jsphon', 'pear.hawklab.jp');
$pkg2xml->setPearinstallerDep('1.4.0'); // PEAR 1.4.0 - 
$pkg2xml->addMaintainer(
    'lead', 
    'msakamoto-sf', 
    'Masahiko Sakamoto', 
    'msakamoto-sf@users.sourceforge.net'
    );
$pkg2xml->setLicense(
    'Apache License, Version 2.0', 
    'http://www.apache.org/licenses/LICENSE-2.0'
    );
$pkg2xml->generateContents();

// get a PEAR_PackageFile object
$pkg1xml =& $pkg2xml->exportCompatiblePackageFile1();

if (isset($_GET['make']) || 
    (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $pkg1xml->writePackageFile();
    $pkg2xml->writePackageFile();
} else {
    $pkg1xml->debugPackageFile();
    $pkg2xml->debugPackageFile();
}

/**
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 * vim: set expandtab tabstop=4 shiftwidth=4:
 */
