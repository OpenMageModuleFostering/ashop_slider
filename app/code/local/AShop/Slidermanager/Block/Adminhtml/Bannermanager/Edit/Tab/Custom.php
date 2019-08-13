<?php
/**
 * AShop Slider
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the AccessshopThemes.com license that is
 * available through the world-wide-web at this URL:
 * http://www.accessshopThemes.com
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @copyright 	Copyright (c) 2015 Accessshop (http://www.accessshopThemes.com)
 * @license     http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Accessshop Block
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @author  	AccessShop Developer
 */
class AShop_Slidermanager_Block_Adminhtml_Bannermanager_Edit_Tab_Custom extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('customgrid');
        $this->setDefaultSort('bannermanager_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_custom' => 1));
        }
    }
    
    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_custom') {
            $bannerIds = $this->_getSelectedBanners();

            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('slidermanager_id', array('in' => $bannerIds));
            } else {
                if ($bannerIds) {
                    $this->getCollection()->addFieldToFilter('slidermanager_id', array('nin' => $bannerIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('slidermanager/slidermanager')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
      $this->addColumn('in_custom', array(
        'header_css_class' => 'a-center',
        'type' => 'checkbox',
        'name' => 'in_custom',
        'align' => 'center',
        'index' => 'slidermanager_id',
        'values' => $this->_getSelectedBanners(),
        ));
        
      $this->addColumn('slidermanager_id', array(
          'header'    => Mage::helper('slidermanager')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'slidermanager_id',
      ));
      
      $this->addColumn('bannermanager_id', array(
            'header' => Mage::helper('slidermanager')->__('Current Slider'),
            'index' => 'bannermanager_id',
            'type' => 'options',
            'options' => Mage::getSingleton('slidermanager/select')->getOptionHash(),
        ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('slidermanager')->__('Slider Name'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      
      $this->addColumn('created_time', array(
            'header' => Mage::helper('slidermanager')->__('Start Date'),
            'type' => 'datetime',
            'index' => 'created_time'
        ));

        $this->addColumn('update_time', array(
            'header' => Mage::helper('slidermanager')->__('End Date'),
            'type' => 'datetime',
            'index' => 'update_time'
        ));
        
        $this->addColumn('order_banner_slider', array(
            'header' => Mage::helper('slidermanager')->__('Order'),
            'name' => 'order_banner_slider',
            'index' => 'order_banner_slider',
            'width' => 0,
            'editable' => true,            
        ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('slidermanager')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('slidermanager')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	  
      return parent::_prepareColumns();
  }

    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/customGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

    public function getRowUrl($row) {
        return '';
    }

    public function getSelectedSliderBanners() {

        $tm_id = $this->getRequest()->getParam('id');
        if (!isset($tm_id)) {
            return array();
        }
        $collection = Mage::getModel('slidermanager/slidermanager')->getCollection();
        $collection->addFieldToFilter('bannermanager_id', $tm_id);

        $bannerIds = array();
        foreach ($collection as $obj) {
            $bannerIds[$obj->getId()] = array('order_banner_slider' => $obj->getOrderBanner());
        }
        return $bannerIds;
    }
    
    protected function _getSelectedBanners() {
        $banners = $this->getRequest()->getParam('slidermanager');
        if (!is_array($banners)) {
            $banners = array_keys($this->getSelectedSliderBanners());
        }
        return $banners;
    }

}