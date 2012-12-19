<?php

if (!defined('_JEXEC')) die('not allowed');


class gateway
{

    protected static $instances = array();

    protected static $categories = array();

	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 */
	public function __construct($options)
	{
		$this->dbo=$options['dbo'];
	    // Set class options.
		$this->options = $options;
	}

	public static function getInstance($options = array())
	{
		// Sanitize the connector options.
		$options['gateway'] = isset($options['gateway']) ?  $options['gateway'] : 'nachrichten';

		// Get the options signature for the gateway.
		$signature = md5(serialize($options));

		// If we already have a parser connector instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{
			// Derive the class name from the gateway.
			$class = 'dataset' . ucfirst(strtolower($options['gateway']));
			// load the file
		    $file=JPATH_BASE.'/gateway/dataset/'.strtolower($options['gateway']).'.php';
		    if (file_exists($file))
		    {
		        require $file;
		    }

			// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
			if (!class_exists($class))
			{
				throw new RuntimeException(sprintf('Unable to load Dataset: %s', $options['gateway'].'# '.$class));
			}

			// Create our new dataset
			try
			{
				$instance = new $class($options);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(sprintf('Unable to create the parser instance: %s', $e->getMessage()));
			}

			// Set the new connector to the global instances based on signature.
			self::$instances[$signature] = $instance;
		}

		return self::$instances[$signature];
	}

	public function getContentItems($categoryid,$limit=5,$includeChildCategories=true)
	{
   		/**
		 * we have to create an application object so that used classes get a valid object
		 */
		$app=JFactory::getApplication('site');

        jimport('joomla.application.component.model');
        $com_path = JPATH_SITE.'/components/com_content/';
        require_once $com_path.'router.php';
        require_once $com_path.'helpers/route.php';
        JModel::addIncludePath($com_path . '/models', 'ContentModel');

        // Get an instance of the generic articles model
        $articles = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams();
        $articles->setState('params', $appParams);

        // Set the filters based on the module params
        $articles->setState('list.start', 0);
        $articles->setState('list.limit', (int) $limit);
        $articles->setState('filter.published', 1);
        $articles->setState('filter.subcategories', $includeChildCategories);
        $articles->setState('filter.featured', true);

        // Ordering
        $articles->setState('list.ordering', 'a.created');
        $articles->setState('list.direction', 'DESC');

        // Access filter, only public because we are a script and not a regual user
        $articles->setState('filter.access', 1);

        // url magic
		jimport('joomla.environment.uri');
		$uri = JURI::getInstance();
		$path = $uri->toString(array('path'));
		$root_path = substr($path, 0,strpos($path, 'xdata')-1);

		$start =strlen($root_path.'/xdata/app');

        $articles->setState('filter.category_id', $categoryid);
        $items = $articles->getItems();
        for($i=0,$n=count($items);$i<$n;$i++)
        {
            $item=$items[$i];
            $item->slug = $item->id.':'.$item->alias;
            $item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;
            $link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
            $item->link = $root_path.substr($link,$start);
            $items[$i]=$item;
        }
        return $items;
	}


}