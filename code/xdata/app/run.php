<?php
/**
 * An example command line application built on the Joomla Platform.
 *
 * To run this example, adjust the executable path above to suite your operating system,
 * make this file executable and run the file.
 *
 * Alternatively, run the file using:
 *
 * php -f run.php
 *
 * Note, this application requires configuration.php and the connection details
 * for the database may need to be changed to suit your local setup.
 *
 * @package    Joomla.Examples
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// We are a valid Joomla entry point.
define('_JEXEC', 1);

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));

// include the platform
require JPATH_BASE.'/platform/libraries/import.php';

// load parser class
require JPATH_BASE.'/gateway/gateway.php';

define ('JPATH_SITE',JPATH_BASE.'/../..');

define('JPATH_CACHE',JPATH_SITE.'/cache' );

define ('JPATH_ADMINISTRATOR',JPATH_SITE.'/administrator');
define ('JPATH_INSTALLATION',JPATH_SITE.'/installation');
define ('JPATH_CONFIGURATION',JPATH_SITE);

/**
 * get data from the upcc directory
 *
 */
class DatabaseApp extends JApplicationWeb
{
	/**
	 * A database object for the application to use.
	 *
	 * @var    JDatabase
	 */
	protected $dbo = null;

	/**
	 * Class constructor.
	 * @throws  JDatabaseException
	 */
	public function __construct()
	{
		// Call the parent __construct method so it bootstraps the application class.
		parent::__construct();

		jimport('joomla.database.database');

		// Note, this will throw an exception if there is an error
		// creating the database connection.
		$this->dbo = JDatabase::getInstance(
			array(
				'driver' => $this->get('dbtype'),
				'host' => $this->get('host'),
				'user' => $this->get('user'),
				'password' => $this->get('password'),
				'database' => $this->get('db'),
				'prefix' => $this->get('dbprefix'),
			)
		);
	}

	/**
	 * Execute the application.
	 */
	public function execute()
	{
		// Request variables
		$gateway=$this->input->get('gateway');
		//echo "RequestVar=".$gateway.'<br />';
		$options = array('gateway' => $gateway);
		$options['dbo'] = $this->dbo;

        $result = gateway::getInstance($options)->execute();
	    echo $result;
        return true;
	}
}

// Wrap the execution in a try statement to catch any exceptions thrown anywhere in the script.
try
{
	// Instantiate the application object, passing the class name to JApplicationCli::getInstance
	// and use chaining to execute the application.
	//JApplicationCli::getInstance('DatabaseApp')->execute();
	JApplicationWeb::getInstance('DatabaseApp')->execute();
}
catch (Exception $e)
{
	// An exception has been caught, just echo the message.
	//fwrite(STDOUT, $e->getMessage() . "\n");
	echo $e->getMessage() . "\n";
	exit($e->getCode());
}
