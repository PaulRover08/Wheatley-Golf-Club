<?php

/**
 * Admin Index Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_IndexController extends Sugarcane_Controllers_Base
{
    public function indexAction()
    {
        $this->view->contentView = '/admin/index/index.phtml';
        $this->renderView('admin.phtml');
    }
}