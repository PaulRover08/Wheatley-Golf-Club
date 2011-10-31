<?php

/**
 * Admin Events Controller
 *
 * @author Anto Heley <anto@antodev.com>
 * @version 1.0
 */

class Admin_EventsController extends Sugarcane_Controllers_Base
{
    public function preDispatch()
    {
        $loggedIn = isset($_SESSION['loggedin']) ? true : false;
        
        if(!$loggedIn) {
            header('Location: /admin/login/');
        }
    }
    
    public function indexAction()
    {
        $filter['event_date'] = 'all';
        $filter['limit']      = 10000;
        $this->view->events = $this->dbMapper->getEvents($filter);
        
        if(!empty($_SESSION['message'])) {
            $this->view->message = $_SESSION['message'];
            unset($_SESSION['message']);
        }
        
        $this->view->css[] = '/css/pagebuilder.css';
        $this->view->contentView = '/admin/events/index.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function eventAction()
    {
        $eventId = $this->req->getParam('event_id');
        $event = array('additionalFields'=>array());
        
        if($eventId) {
            $filter['event_date'] = 'all';
            $filter['event_id'] = $eventId;
            $this->view->event_id = $eventId;
            
            $event = $this->dbMapper->getEvents($filter);
            $event = array_pop($event);
            $event['additionalFields'] = $this->dbMapper->getExtraEventDetails($eventId);
        }
        
        $eventfields = $this->dbMapper->getEventFormFields();
        
        // If we have post data set in the session, we need to use the session data rather than the database data
        if(isset($_SESSION['formdata'])) {
            foreach($eventfields as $field) {
                if(isset($_SESSION['formdata']['data'][$field['fieldname']])) {
                    $event['additionalFields'][$field['fieldname']] = $_SESSION['formdata']['data'][$field['fieldname']];
                }
            }
            unset($_SESSION['formdata']);
        }
        
        // Send the form data to the view
        $this->view->event = $event;

        // Setup the additional fields
        $form = new Sugarcane_FormBuilder_Form($eventfields, $event['additionalFields']);
        $this->view->formHtml = $form->renderForm();
        
        // Render the whole page
        $this->view->contentView = '/admin/events/event.phtml';
        $this->renderView('admin.phtml');
    }
        
    public function saveeventAction()
    {   
        // Lets save the first set of fields
        $save['event_id']    = $this->req->getParam('event_id');
        $save['title']       = $this->req->getParam('title');
        $save['eventdate']   = $this->req->getParam('eventdate');
        $save['summary']     = $this->req->getParam('summary');
        $save['description'] = $this->req->getParam('description');
        $save['published']   = $this->req->getParam('published');
        
        $event_id = $this->dbMapper->saveRecord($save, 'events', 'event_id');
        
        $eventfields = $this->dbMapper->getEventFormFields();
        $form = new Sugarcane_FormBuilder_Event($eventfields, $this->req->getParams(), $event_id);
        
        if($form->save($this->dbMapper)) {
            $this->_redirect('/admin/events/');
        } else {
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
    public function manageformAction()
    {
        $this->view->fields = $this->dbMapper->getEventFormFields();
        
        // Render the whole page
        $this->view->css[] = '/css/pagebuilder.css';
        $this->view->contentView = '/admin/events/fieldlist.phtml';
        $this->renderView('admin.phtml');
    }
    
    public function editfieldAction()
    {
        // Setup the possible types of field the form can have
        $possibleFieldTypes = array('dropdown' => 'Drop down menu',
                                    'textbox'  => 'Text box');
        
        // Get the field ID from the URL if available and pre-set all the field data
        $event_field_id = $this->req->getParam('event_field_id', '');
        $this->view->field_id = $event_field_id;
        
        $fieldInfo = array();
        if($event_field_id) {
            $fieldInfo = $this->dbMapper->getEventFormFieldById($event_field_id);
        }
        $this->view->field = $fieldInfo;
        
        // Send the drop down field options to the view with selected value
        $fieldType = isset($fieldInfo['field_type']) ? $fieldInfo['field_type'] : '';
        $this->view->dropDownOptions = Globals::makeOptions($possibleFieldTypes, $fieldType);
        
        // Let the view know now what type of field this is
        $this->view->fieldType = $fieldType;
        
        // If the field is a dropdown / radio we need to explode the options into a key=>value array
        if($fieldType == 'dropdown' || $fieldType == 'radio') {
            $extra_details = explode('::', $fieldInfo['extra_details']);
            
            $this->view->extra_details = array();
            foreach($extra_details as $detail) {
                $this->view->extra_details[$detail] = $detail;
            }
        }
        
        // Render the whole page
        $this->view->css[] = '/css/pagebuilder.css';
        $this->view->js[]  = '/js/formbuilder.js';
        $this->view->contentView = '/admin/events/editfield.phtml';
        $this->renderView('admin.phtml');
    }
}