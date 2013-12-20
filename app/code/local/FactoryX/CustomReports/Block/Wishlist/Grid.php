<?phpclass FactoryX_CustomReports_Block_Wishlist_Grid extends Mage_Adminhtml_Block_Widget_Grid{    public function __construct()    {        parent::__construct();        $this->setId('wishlistReportGrid');        $this->setDefaultSort('updated_at');        $this->setDefaultDir('desc');    }		public function getFilterWishlistWithItems()    {					$wishlistItemsCollection = Mage::getModel('wishlist/item')									->getCollection();		// Array of wishlist items linked to a wishlist that will be used to filter		$filterArray = array();		        foreach ($wishlistItemsCollection->getItems() as $item)        {            $wishlist_id = $item->getWishlistId();						if (!in_array($wishlist_id, $filterArray)) 			{				array_push($filterArray, $wishlist_id);			}        }		        return $filterArray;    }    protected function _prepareCollection()    {		// Get the session		$session = Mage::getSingleton('core/session');				// Dates for one week		$store = Mage_Core_Model_App::ADMIN_STORE_ID;		$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);		date_default_timezone_set($timezone);				// Automatic -30 days if no dates provided		if ($session->getWishlistFrom())		{			$sDate = $session->getWishlistFrom();		}        else		{			$sDate = date('Y-m-d 00:00:00',				Mage::getModel('core/date')->timestamp(strtotime('-30 days'))			);		}		if ($session->getWishlistTo())		{			$eDate = $session->getWishlistTo();		}        else		{			$eDate = date('Y-m-d 23:59:59', 				Mage::getModel('core/date')->timestamp(time())			);		}				###############################################################################		$start = new Zend_Date($sDate);		$start->setTimeZone("UTC");        $end = new Zend_Date($eDate);		$end->setTimeZone("UTC");		###############################################################################		$from = $start->toString("Y-MM-dd HH:mm:ss");		$to = $end->toString("Y-MM-dd HH:mm:ss");        $collection = Mage::getModel('wishlist/wishlist')						->getCollection()						->addFieldToFilter('wishlist_id',array('in'=>$this->getFilterWishlistWithItems()))						->addFieldToFilter('updated_at', array('gt'	=>	$from))						->addFieldToFilter('updated_at', array('lt'	=>	$to));						//->joinLeft('customer_entity', 'main_table.customer_id=customer_entity.entity_id', array('email'));            //->addAttributeToSelect('entity_id')			//->addAttributeToSelect('customer_id')			//->addAttributeToSelect('updated_at')        $this->setCollection($collection);        parent::_prepareCollection();        return $this;    }    protected function _prepareColumns()    {        $this->addColumn('wishlist_id', array(            'header'    =>Mage::helper('reports')->__('Wishlist ID'),            'width'     =>'50px',            'index'     =>'wishlist_id'        ));        $this->addColumn('customer_id', array(            'header'    =>Mage::helper('reports')->__('Customer ID'),			'width'     =>'50px',            'index'     =>'customer_id'        ));				$this->addColumn('shared', array(            'header'    =>Mage::helper('reports')->__('Has Been Shared ?'),			'width'     =>'50px',            'index'     =>'shared',			'type'      => 'options',            'options'   => array(                0  => Mage::helper('reports')->__('No'),                1  => Mage::helper('reports')->__('Yes')            )        ));        $this->addColumn('updated_at', array(            'header'    =>Mage::helper('reports')->__('Updated At'),            'align'     =>'right',			'type'      =>'datetime',            'index'     =>'updated_at'        ));				$this->addColumn('action',            array(                'header'    =>  Mage::helper('reports')->__('Action'),                'width'     => '100',                'type'      => 'action',                'getter'    => 'getCustomerId',                'actions'   => array(                    array(                        'caption'   => Mage::helper('reports')->__('Edit Customer'),                        'url'       => array('base'=> 'adminhtml/customer/edit'),                        'field'     => 'id'                    )                ),                'filter'    => false,                'sortable'  => false,                'index'     => 'stores',                'is_system' => true,        ));        $this->addExportType('*/*/exportWishlistCsv', Mage::helper('reports')->__('CSV'));        $this->addExportType('*/*/exportWishlistExcel', Mage::helper('reports')->__('Excel'));        return parent::_prepareColumns();    }}