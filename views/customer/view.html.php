<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeprolab
 * @link            http://jeprodev.net

 * @copyright (C)   2009 - 2011
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of,
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JeprolabCustomerViewCustomer extends JViewLegacy
{
	protected $customer;

    protected $helper;
    
    protected $customers;
    
    protected $context;
    
    public $pagination;
    
	public function renderDetails($tpl = null){
		if($this->getLayout() !== 'modal'){
            
        }
        
        $customerModel = new JeprolabCustomerModelCustomer();
        $this->customers = $customerModel->getCustomerList();
        $this->pagination = $customerModel->getPagination();
        if($this->getLayout() != 'modal'){
            $this->addToolBar();
            $this->sideBar = JHtmlSidebar::render();
        }
        parent::display($tpl);
	}

    public function renderAddForm($tpl = null){
        if($this->context == null){ $this->context = JeprolabContext::getContext(); }
        $groups = JeprolabGroupModelGroup::getGroups($this->context->language->lang_id, true);

        $this->assignRef('groups', $groups);
        $this->helper = new JeprolabHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function renderEditForm($tpl = null){
        if($this->context == null){ $this->context = JeprolabContext::getContext(); }
        $groups = JeprolabGroupModelGroup::getGroups($this->context->language->lang_id, true);

        $this->assignRef('groups', $groups);
        $this->helper = new JeprolabHelper();
        $this->addToolBar();
        $this->sideBar = JHtmlSidebar::render();
        parent::display($tpl);
    }

	public function renderView($tpl = null){
		if($this->getLayout() !== 'modal'){
	
		}
		if($this->context == null){ $this->context = JeprolabContext::getContext(); }
		$db = JFactory::getDBO();
		$this->setLayout('view');
		
		$this->loadObject();
		if(!JeprolabTools::isLoadedObject($this->customer, 'customer_id')){ return; }
		$this->context->customer = $this->customer;
				
		$customer_stats = $this->customer->getStats();
		$query = "SELECT SUM(total_paid_real) FROM " . $db->quoteName('#__jeprolab_orders');
		$query .= " WHERE customer_id = " . (int)$this->customer->customer_id . " AND valid = 1";
		$db->setQuery($query);
		$total_customer = $db->loadResult();
		if($total_customer){
			$query = "SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM " . $db->quoteName('#__jeprolab_orders');
			$query .= " WHERE valid = 1 AND customer_id != ".(int)$this->customer->customer_id . " GROUP BY ";
			$query .= "customer_id HAVING SUM(total_paid_real) > " . (int)$total_customer;
			$db->setQuery($query);
			$db->loadResult();
			$count_better_customers = (int)$db->loadResult('SELECT FOUND_ROWS()') + 1;
		}else{
			$count_better_customers = '-';
		}
		$orders = JeprolabOrderModelOrder::getCustomerOrders($this->customer->customer_id, true);
		$total_orders = count($orders);
		for ($i = 0; $i < $total_orders; $i++){
			$orders[$i]->total_paid_real_not_formated = $orders[$i]->total_paid_real;
			$orders[$i]->total_paid_real = JeprolabTools::displayPrice($orders[$i]->total_paid_real, new JeprolabCurrencyModelCurrency((int)$orders[$i]->currency_id));
		}
		
		$messages = JeprolabCustomerThreadModelCustomerThread::getCustomerMessages((int)$this->customer->customer_id);
		$total_messages = count($messages);
		for ($i = 0; $i < $total_messages; $i++){
			$messages[$i]->message = substr(strip_tags(html_entity_decode($messages[$i]->message, ENT_NOQUOTES, 'UTF-8')), 0, 75);
			$messages[$i]->date_add = Tools::displayDate($messages[$i]->date_add, null, true);
		}
		
		$groups = $this->customer->getGroups();
		$total_groups = count($groups);
		for ($i = 0; $i < $total_groups; $i++){
			$group = new JeprolabGroupModelGroup($groups[$i]);
			$groups[$i] = array();
			$groups[$i]['group_id'] = $group->group_id;
			$groups[$i]['name'] = $group->name[$this->context->controller->default_form_language];
		}

		$total_ok = 0;
		$orders_ok = array();
		$orders_ko = array();
		foreach ($orders as $order){
			if (!isset($order->order_state)){
				$order->order_state = JText::_('COM_JEPROLAB_THERE_IS_NO_STATUS_DEFINED_FOR_THIS_ORDER_MESSAGE');
			}
			if ($order->valid){
				$orders_ok[] = $order;
				$total_ok += $order->total_paid_real_not_formated;
			}else{
				$orders_ko[] = $order;
			}
		}
		
		$products = $this->customer->getBoughtProducts();
		
		$carts = JeprolabCartModelCart::getCustomerCarts($this->customer->customer_id);
		$total_carts = count($carts);
		for ($i = 0; $i < $total_carts; $i++){
			$cart = new JeprolabCartModelCart((int)$carts[$i]->cart_id);
			$this->context->cart = $cart;
			$summary = $cart->getSummaryDetails();
			$currency = new JeprolabCurrencyModelCurrency((int)$carts[$i]->currency_id);
			$carrier = new JeprolabCarrierModelCarrier((int)$carts[$i]->carrier_id);
			$carts[$i]['id_cart'] = sprintf('%06d', $carts[$i]['id_cart']);
			$carts[$i]['date_add'] = JeprolabValidator::displayDate($carts[$i]->date_add, null, true);
			$carts[$i]['total_price'] = Tools::displayPrice($summary->total_price, $currency);
			$carts[$i]->name = $carrier->name;
		}
		
		$query = "SELECT DISTINCT cart_product.product_id, cart.cart_id, cart.lab_id, cart_product.lab_id ";
		$query .= " AS cart_product_lab_id FROM " . $db->quoteName('#__jeprolab_cart_product') . " AS cart_product";
		$query .= " JOIN " . $db->quoteName('#__jeprolab_cart') . " AS cart ON (cart.cart_id = cart_product.cart_id) ";
		$query .= "JOIN " . $db->quoteName('#__jeprolab_product') . " AS product ON (cart_product.product_id = product.";
		$query .= "product_id) WHERE cart.customer_id = " . (int)$this->customer->customer_id . " AND cart_product.product_id";
		$query .= " NOT IN ( SELECT product_id FROM " . $db->quoteName('#__jeprolab_orders') . " AS ord JOIN ";
		$query .= $db->quoteName('#__jeprolab_order_detail') . " AS ord_detail ON (ord.order_id = ord_detail.order_id ) WHERE ";
		$query .= "ord.valid = 1 AND ord.customer_id = " . (int)$this->customer->customer_id . ")";
		
		$db->setQuery($query);
		$interested = $db->loadObjectList();
		$total_interested = count($interested);
		for ($i = 0; $i < $total_interested; $i++){
			$product = new JeprolabProductModelProduct($interested[$i]->product_id, false,
					$this->context->controller->default_form_language, $interested[$i]->lab_id);
			if (!Validate::isLoadedObject($product, 'product_id')){ continue; }
					
			$interested[$i]->url = $this->context->controller->getProductLink(
				$product->product_id, $product->link_rewrite,
				JeprolabCategoryModelCategory::getLinkRewrite($product->default_category_id,
				$this->context->controller->default_form_language), null, null, $interested[$i]->cp_lab_id
			);
			$interested[$i]->product_id = (int)$product->product_id;
			$interested[$i]->name = htmlentities($product->name);
		}

		$connections = $this->customer->getLastConnections();
		if (!is_array($connections))
			$connections = array();
		$total_connections = count($connections);
					
		for ($i = 0; $i < $total_connections; $i++){
			$connections[$i]->http_referer = $connections[$i]->http_referer ? preg_replace('/^www./', '', parse_url($connections[$i]->http_referer, PHP_URL_HOST)) : JText::_('COM_JEPROLAB_DIRECT_LINK_LABEL');
		}
		$referrers = JeprolabReferrerModelReferrer::getReferrers($this->customer->customer_id);
		$total_referrers = count($referrers);
		for ($i = 0; $i < $total_referrers; $i++){
			$referrers[$i]->date_add = JeprolabTools::displayDate($referrers[$i]->date_add,null , true);
		}
		$customerLanguage = new JeprolabLanguageModelLanguage($this->customer->lang_id);
		$lab = new JeprolabShopModelShop($this->customer->lab_id);
		
		//$this->assignRef('customer', $customer);
			/*'gender' => $gender,
		/*	'gender_image' => $gender_image,
		// General information of the customer */
        $registration = JeprolabTools::displayDate($this->customer->date_add,null , true);
		$this->assignRef('registration_date', $registration);
		$this->assignRef('customer_stats', $customer_stats);
        $last_visit = JeprolabTools::displayDate($customer_stats->last_visit,null , true);
		$this->assignRef('last_visit', $last_visit);
		$this->assignRef('count_better_customers', $count_better_customers);
        $lab_feature_active = JeprolabShopModelShop::isFeaturePublished();
		$this->assignRef('lab_is_feature_active', $lab_feature_active);
		$this->assignRef('lab_name', $lab->lab_name);
        $customerBirthday = JeprolabTools::displayDate($this->customer->birthday);
		$this->assignRef('customer_birthday', $customerBirthday);
        $last_update = JeprolabTools::displayDate($this->customer->date_upd, null , true);
		$this->assignRef('last_update', $last_update);
        $customerExists = JeprolabCustomerModelCustomer::customerExists($this->customer->email);
		$this->assignRef('customer_exists', $customerExists);
		$this->assignRef('lang_id', $this->customer->lang_id);
		$this->assignRef('customerLanguage', $customerLanguage);
		// Add a Private note
        $customerNote = JeprolabTools::htmlentitiesUTF8($this->customer->note);
		$this->assignRef('customer_note', $customerNote);
		// Messages
		$this->assignRef('messages', $messages);
		// Groups
		$this->assignRef('groups', $groups);
		// Orders
		$this->assignRef('orders', $orders);
		$this->assignRef('orders_ok', $orders_ok);
		$this->assignRef('orders_ko', $orders_ko);
        $total_ok = JeprolabTools::displayPrice($total_ok, $this->context->currency->currency_id);
		$this->assignRef('total_ok', $total_ok);
		// Products
		$this->assignRef('products', $products);
		// Addresses
        $addresses = $this->customer->getAddresses($this->context->controller->default_form_language);
		$this->assignRef('addresses', $addresses);
		// Discounts
        $discounts = JeprolabCartRuleModelCartRule::getCustomerCartRules($this->context->controller->default_form_language, $this->customer->customer_id, false, false);
		$this->assignRef('discounts', $discounts);
		// Carts
		$this->assignRef('carts', $carts);
		// Interested
		$this->assignRef('interested_products', $interested);
		// Connections
		$this->assignRef('connections', $connections);
		// Referrers
		$this->assignRef('referrers', $referrers);
		
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}
	
	/**
	 * Load class object using identifier in $_GET (if possible)
	 * otherwise return an empty object, or die
	 *
	 * @param boolean $opt Return an empty object if load fail
	 * @return object|boolean
	 */
	public function loadObject($opt = false){
		/*if (!isset($this->customer) || empty($this->customer))
			return true;
		*/
		$app = JFactory::getApplication();
		$customer_id = (int)$app->input->get('customer_id');
		if ($customer_id && JeprolabTools::isUnsignedInt($customer_id)){
			if (!$this->customer)
				$this->customer = new JeprolabCustomerModelCustomer($customer_id);
			if (JeprolabTools::isLoadedObject($this->customer, 'customer_id')){
				return $this->customer;
			}
			// throw exception
			//$this->errors[] = Tools::displayError('The object cannot be loaded (or found)');
			return false;
		}elseif ($opt){
			if (!$this->customer)
				$this->customer = new JeprolabCustomerModelCustomer();
			return $this->customer;
		}else{
			$this->errors[] = Tools::displayError('The object cannot be loaded (the identifier is missing or invalid)');
			return false;
		}
	}
	
	public function viewThreads($tpl = null){
		if($this->getLayout() != 'modal'){
			$this->addToolBar();
			$this->sideBar = JHtmlSidebar::render();
		}
		parent::display($tpl);
	}

	private function addToolBar(){
		switch ($this->getLayout()){
			case 'add':
				JToolBarHelper::title(JText::_('COM_JEPROLAB_VIEW_CUSTOMER_TITLE'), 'jeprolab-order');
				JToolBarHelper::apply('save');
				JToolBarHelper::cancel('cancel');
				break;
			default:
				JToolBarHelper::title(JText::_('COM_JEPROLAB_CUSTOMERS_LIST_TITLE'), 'jeprolab-order');
				JToolBarHelper::addNew('add');
				break;
		}
		JeprolabHelper::sideBarRender('customer');
	}

    protected function renderSubMenu($current = 'customer'){
        $script = '<div class="box_wrapper jeprolab_sub_menu_wrapper"><fieldset class="btn-group">';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=customer') . '" class="btn jeprolab_sub_menu ' . (($current == 'customer' ) ? 'btn-success' : '') . '" ><i class="icon-customer" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_CUSTOMERS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=address') . '" class="btn jeprolab_sub_menu ' . (($current == 'address' ) ? 'btn-success' : '') . '" ><i class="icon-address" ></i> '. ucfirst(JText::_('COM_JEPROLAB_ADDRESSES_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=group') . '" class="btn jeprolab_sub_menu ' . (($current == 'group' ) ? 'btn-success' : '') . '" ><i class="icon-group" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_GROUPS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=cart') . '" class="btn jeprolab_sub_menu ' . (($current == 'cart' ) ? 'btn-success' : '') . '" ><i class="icon-cart" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_SHOPPING_CARTS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=customer&task=threads') . '" class="btn jeprolab_sub_menu ' . (($current == 'threads' ) ? 'btn-success' : '') . '" ><i class="icon-thread" ></i> ' .  ucfirst(JText::_('COM_JEPROLAB_CUSTOMER_THREADS_LABEL')) . '</a>';
        $script .= '<a href="' . JRoute::_('index.php?option=com_jeprolab&view=contact') . '" class="btn jeprolab_sub_menu ' . (($current == 'contact' ) ? 'btn-success' : '') . '" ><i class="icon-contact" ></i> ' . ucfirst(JText::_('COM_JEPROLAB_CONTACTS_LABEL')) . '</a>';
        $script .= '</fieldset></div>';
        return $script;
    }
}