<?php
defined('SYSPATH') or die('No direct script access.');

class ZForm_Controller extends Controller_Admin
{

    protected $_this_model = null;

    /**
     * array(
     * 'id' => array(
     * 'id' => __('ID')
     * ),
     * 'title' => array(
     * 'name' => __('title'),
     * 'sortable' => false
     * ),
     * 'last_login' => array(
     * 'label' => __('Last login')
     * ),
     * 'logins' => array(
     * 'label' => __('# of logins')
     * )
     * );
     *
     * @var array
     */
    protected $column_list = array();

    public function before()
    {
        parent::before();
        $this->_initialize();
    }

    public function action_edit()
    {
        $id = (int) Request::$current->param('id');
        $model = ORM::factory($this->_this_model, $id);
        if (Request::$current->method() === Request::POST) {
            $model->get_form()->save();
            Message::add('success', __('Form saved'));
            // do somethign
        }
        $this->template->content = View::factory('zform/wrappers/edit', array(
            'form' => $model->generate_form()
        ));
    }

    public function action_index()
    {
        // set the template title (see Controller_App for implementation)
        $this->template->title = __('User administration');
        // create a user
        $user = ORM::factory($this->_this_model);
        // This is an example of how to use Kohana pagination
        // Get the total count for the pagination
        $total = $user->count_all();
        // Create a paginator
        $pagination = Pagination::factory(array(
            'total_items' => $total,
            'items_per_page' => 30, // set this to 30 or 15 for the real thing, now just for testing purposes...
            'auto_hide' => false,
            'view' => 'pagination/floating'
        ))->route_params(array(
            'directory' => Request::current()->directory(),
            'controller' => Request::current()->controller(),
            'action' => Request::current()->action()
        ));
        // Get the items for the query
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id'; // set default sorting direction here
        $dir = isset($_GET['dir']) ? 'DESC' : 'ASC';
        $result = $user->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by($sort, $dir)
            ->find_all();
        // render view
        // pass the paginator, result and default sorting direction
        $this->template->content = View::factory('zform/wrappers/index')->set('result', $result)
            ->set('paging', $pagination)
            ->set('default_sort', $sort)
            ->set('column_list', $this->column_list);
    }

    protected function _initialize()
    {}
}