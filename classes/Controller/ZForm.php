<?php
defined('SYSPATH') or die('No direct script access.');

abstract class Controller_ZForm extends Controller_Redback
{

    /**
     *
     * @var String default model for this controller where list and del methods will be called.
     */
    public $defaultModel = NULL;

    public $title = NULL;

    public $title_info = 'form_complete_fields_message';

    public $sub_controller = NULL;

    public $sub_link = NULL;

    public $sub_link_title = NULL;

    public $has_image = FALSE;

    /**
     * array(
     * 'column'=>mixed,
     * 'op'=> string,
     * 'value'=>mixed
     * )
     *
     * @var array
     */
    public $zform_list_rule = array();

    public $view_edit = NULL;

    public $view_list = NULL;

    public $view_extra_links = NULL;

    public $_feature_id;

    public $_add_new = TRUE;

    public $img_folder = '';

    public $img_is_core = FALSE;

    public $img_fields = array(
        'img'
    );

    /**
     * set true for zform to gererate deafault breadcrumb
     *
     * @var boolean
     */
    public $breadcrumb;

    private function create_breadcrumb()
    {
        if (empty($this->breadcrumb)) {
            $controller = Request::$initial->controller();
            $action = Request::$initial->action();
            
            if (! empty($this->title)) {
                $controller_name = __($this->title);
                // } else if (strstr ( $controller, 'Admin_' )) {
                // $controller_name = substr ( $controller, 6 );
            } else {
                $controller_name = Inflector::humanize($controller);
            }
            $breadcrumb = $this->get_home_breadcrumb();
            
            switch ($action) {
                case 'index':
                    {
                        $breadcrumb .= '<li class="page-title">' . HTML::anchor(URL::site($controller), $controller_name) . '</li>';
                        if ($this->_add_new) {
                            $breadcrumb .= '<li class="add-new">' . View::factory($this->admin_template . '/zform/wrappers/addnew') . '</li>';
                        }
                        break;
                    }
                case 'edit':
                    {
                        $breadcrumb .= '<li>' . HTML::anchor(URL::site($controller), $controller_name) . '</li>';
                        if (! empty($this->id)) {
                            $breadcrumb .= '<li class="page-title">' . HTML::anchor(URL::site($controller . '/' . $action . '/' . $this->id), $this->id) . '</li>';
                        } else {
                            $breadcrumb .= '<li class="page-title">' . HTML::anchor(URL::site($controller . '/' . $action), __('generic_label_edit')) . '</li>';
                        }
                        $breadcrumb .= '<li></li>';
                        break;
                    }
                default:
                    {
                        $breadcrumb .= '<li>' . HTML::anchor(URL::site($controller), $controller_name) . '</li>';
                        if (! empty($this->id)) {
                            $breadcrumb .= '<li class="page-title">' . HTML::anchor(URL::site($controller . '/' . $action . '/' . $this->id), $this->id) . '</li>';
                        } else {
                            $breadcrumb .= '<li class="page-title">' . HTML::anchor(URL::site($controller . '/' . $action), __($action)) . '</li>';
                        }
                        $breadcrumb .= '<li></li>';
                    }
            }
            $this->breadcrumb = $breadcrumb;
        }
    }

    protected function get_home_breadcrumb()
    {
        $controller = Request::$initial->controller();
        if (strstr($controller, 'Admin_')) {
            return '<li>' . HTML::anchor(URL::site('admin'), __('dashboard_breadcrumb_home_link')) . '</li>';
        }
        return '<li>' . HTML::anchor(URL::site('/'), __('Home')) . '</li>';
    }

    public function after()
    {
        $this->create_breadcrumb();
        Redback::instance()->contents('breadcrumb', $this->breadcrumb);
        parent::after();
    }

    public function action_index()
    {
        $this->action_list();
    }

    public function listing()
    {
        $button_action = $this->request->query('button-action');
        if ($button_action == 'delete-confirm') {
            $this->delete();
        } elseif ($button_action == 'delete') {
            Message::add(Message::error, __('Do you want to delete following?'));
        } elseif ($button_action == 'export-csv') {}
    }

    public function action_list()
    {
        $view = $this->get_list_contents();
        Redback::instance()->contents('content', $view);
        Redback::instance()->title(__($this->title));
    }

    protected function get_list_contents()
    {
        // try to find id if sub level controller
        $id = $this->request->param('id', NULL);
        $this->listing();
        // check if any user is logged in or not
        $model = Model::factory($this->defaultModel);
        // only use for node to limit use
        if (isset($this->node_type)) {
            $model->type = $this->node_type;
        }
        if (isset($this->_feature_id)) {
            $model->_feature_id = $this->_feature_id;
        }
        $model_list = $model->tableView($this->zform_list_rule);
        // echo Debug::vars($this->view_list);
        if ($this->view_list) {
            $view = View::factory($this->view_list);
        } else {
            $view = View::factory($this->admin_template . '/zform/index');
        }
        $view->list = $model_list;
        $view->id = $id;
        if ($model->_feature_id) {
            // get feature Name form Model if its set else use title
            $this->title = Features::instance()->get_name($model->_feature_id);
        }
        $view->title = $this->title;
        $view->_add_new = $this->_add_new;
        return $view;
    }

    public function action_edit()
    {
        $id = $this->request->param('id', NULL);
        /**
         *
         * @var $model Zform
         */
        if (is_numeric($id)) {
            $model = ORM::factory($this->defaultModel, $id);
            // print_r($model);
        } else {
            $model = ORM::factory($this->defaultModel);
        }
        // only use for node to limit use
        if (isset($this->node_type)) {
            $model->type = $this->node_type;
        }
        if (isset($this->_feature_id)) {
            $model->_feature_id = $this->_feature_id;
        }
        if (isset($this->node_appearance_id)) {
            $model->appearance_id = $this->node_appearance_id;
        }
        // save the data
        if ($this->request->method() === Request::POST) {
            $model->get_form()->z_save();
        }
        if (Redback::instance()->responseType == 'html') {
            if ($this->view_edit) {
                $view = View::factory($this->view_edit);
            } else {
                $view = View::factory($this->admin_template . '/zform/edit');
            }
            if ($model->_feature_id) {
                // get feature Name form Model if its set else use title
                $this->title = Features::instance()->get_name($model->_feature_id);
            }
            // echo Debug::vars($model);
            // die();
            $view->id = $id;
            $view->content = $model;
            $view->title = $this->title;
            $view->title_info = $this->title_info;
            $view->sub_link = $this->sub_link;
            $view->has_image = $this->has_image;
            $view->extra_links = $this->view_extra_links;
            Redback::instance()->contents('content', $view);
            Redback::instance()->title(__($this->title));
        } else {
            Redback::instance()->objects($model);
        }
    }

    public function action_del()
    {
        if ($this->request->method() == Request::GET) {
            $id = $this->request->param('id', NULL);
            $confirm = $this->request->query('confirm');
            $button_action = $this->request->query('button-action');
            if ($confirm) {
                if (is_numeric($id)) {
                    $model = ORM::factory($this->defaultModel, $id);
                    // make sure page is loaded.
                    if ($model->loaded()) {
                        if (! empty($model->locked)) {
                            Message::add(Message::error, __('can not delete :title :id locked feature', array(
                                ':title' => $this->title,
                                ':id' => $id
                            )));
                        } else {
                            try {
                                $model->delete();
                                Message::add('success', __('Deleted :title :id', array(
                                    ':title' => __($this->title),
                                    ':id' => $id
                                )));
                            } catch (Database_Exception $e) {
                                $message = 'can not delete entry some database issue, try contact support to resolve this issue ';
                                switch ($e->getCode()) {
                                    case 1451:
                                        $message = 'can not delete entry other information linked to this data. please delete them first. ';
                                        break;
                                    default:
                                        break;
                                }
                                Message::add(Message::ERROR, $message);
                            }
                        }
                    } else {
                        Message::add(Message::WARNING, __('Page not found in system ID :id', array(
                            ':id' => $id
                        )));
                    }
                    $controller = $this->request->controller();
                    Request::current()->uri("/{$controller}");
                    Request::current()->action('index');
                }
            } elseif ($button_action == 'delete-confirm') {
                $this->delete();
                $controller = $this->request->controller();
                Request::current()->uri("/{$controller}");
                Request::current()->action('index');
            } else {
                $this->request->query('button-action', 'delete');
                $this->request->query('check', array(
                    $id => $id
                ));
            }
        }
        $this->action_list();
    }

    /**
     * Delete list page entries.
     *
     * @param
     *            String Model that delete function will be invoked.
     */
    public function delete()
    {
        if ($this->defaultModel) {
            $check = (array) $this->request->query('check');
            $deleted = '';
            $notDeleted = '';
            $locked = '';
            foreach ($check as $id) {
                $model = Model_Redback::factory($this->defaultModel, $id);
                try {
                    if (empty($model->locked)) {
                        $model->delete();
                        $deleted .= $id . ', ';
                    } else {
                        $locked .= $id . ', ';
                    }
                } catch (Exception $e) {
                    $notDeleted .= $id . ', ';
                    echo $e;
                }
            }
            if ($deleted) {
                Message::add('success', 'Deleted: ' . $deleted);
            }
            if ($notDeleted) {
                Message::add(Message::error, 'Not Deleted: ' . $notDeleted);
            }
            if ($locked) {
                Message::add(Message::error, 'Locked Enteries: ' . $locked);
            }
            Redirect::redirect("/{$this->request->controller()}/{$this->request->action()}/");
        } else {
            Message::add(Message::error, 'Internal error No Model selected');
        }
    }

    public function _initialize()
    {
        $id = $this->request->param('id', NULL);
        if ($id && $this->sub_controller && $this->sub_link_title) {
            $uri = '/' . $this->sub_controller . '/index/' . $id;
            $this->sub_link = HTML::anchor($uri, __($this->sub_link_title), array(
                'class' => 'new-tab-link'
            ));
        }
    }

    public function action_view()
    {
        if ($this->id) {
            $view = View::factory($this->admin_template . '/zform/view');
            $view->set('id', $this->id);
            Redback::instance()->contents('content', $view);
        }
    }

    public function action_img()
    {
        if ((int) $this->id) {
            $model_sys = ORM::factory($this->defaultModel, $this->id);
            $model = new Model_Generic($model_sys, $this->img_fields, $this->img_folder, $this->img_is_core);
            if (Request::current()->method() == Request::POST) {
                $model->save_form();
            }
            if (Redback::instance()->responseType == 'html') {
                Redback::instance()->title(__('Edit Img'));
                // load the content from view
                $view = View::factory("node/{$this->admin_template}/img");
                $view->set('content', $model);
                Redback::instance()->contents('content', $view);
                if ($this->request->method() == Request::GET) {
                    // Redback::instance ()->scripts ( "//feather.aviary.com/js/feather.js" );
                    Redback::instance()->scripts("/assets/core/js/feather.js");
                }
            } else {
                Redback::instance()->objects($model);
            }
        } else {
            Message::add(Message::error, __('Error: Unsaved Page, no Category ID .'));
        }
    }
}