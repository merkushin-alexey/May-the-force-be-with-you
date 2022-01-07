<?php

use Model\Boosterpack_model;
use Model\Post_model;
use Model\User_model;
use Model\Login_model;
use Model\Comment_model;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {

        parent::__construct();

        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation_many(Post_model::get_all(), 'default');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_boosterpacks()
    {
        $posts =  Boosterpack_model::preparation_many(Boosterpack_model::get_all(), 'default');
        return $this->response_success(['boosterpacks' => $posts]);
    }

    public function login()
    {
        // TODO: task 1, аутентификация
        if ( User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_ALLREADY_LOGGED);
        }
        $login = App::get_ci()->input->post('login');
        $password = App::get_ci()->input->post('password');
        try {
            $user = User_model::find_user_by_email_and_password($login, $password);
        } catch (Exception $exception) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_INTERNAL_ERROR);
        }
        $user = Login_model::login($user);
        return $this->response_success(['user' => User_model::preparation($user, 'default')]);
    }

    public function logout()
    {
        if ( User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_ALLREADY_LOGGED);
        }
        Login_model::logout();
        // TODO: task 1, аутентификация
    }

    public function comment()
    {
        // TODO: task 2, комментирование
        if ( !User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $text = App::get_ci()->input->post('text');
        $reply_id = App::get_ci()->input->post('reply_id');
        $assign_id = App::get_ci()->input->post('assign_id');
        $user_id = App::get_s()->session->id;
        $comment = [
            'text' => $text,
            'user_id' => $user_id,
            'reply_id' => $reply_id,
            'assign_id' => $assign_id,
            'likes' => 0,
            'time_created' => date('Y-m-d H:i', strtotime(now())),
            'time_updated' => date('Y-m-d H:i', strtotime(now())),
        ];
        try {
            $comment = Comment_model::create($comment);
        } catch (Exception $exception) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_INTERNAL_ERROR);
        }
    
        return $this->response_success(['comments' => $comment]);
    }

    public function like_comment(int $comment_id)
    {
        // TODO: task 3, лайк комментария
        if ( !User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $user = User_model::get_user();
        $user->decrement_likes();
        $comment = Comment_model::find_post_by_id($comment_id);
        $comment->increment_likes($user);
        return $this->response_success();
    }

    public function like_post(int $post_id)
    {
        // TODO: task 3, лайк поста
        if ( !User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $user = User_model::get_user();
        $user->decrement_likes();
        try {
            $post = Post_model::find_post_by_id($post_id);
        } catch (Exception $exception) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_INTERNAL_ERROR);
        }
        $post->increment_likes($user);
        return $this->response_success();
    }

    public function add_money()
    {
        // TODO: task 4, пополнение баланса

        $sum = (float)App::get_ci()->input->post('sum');
        $user = new User_model();
        $result = $user->add_money($sum);
        if($result) {
            return $this->response_success();
        }
        else {
            return $this->response_error();
        }

    }

    public function get_post(int $post_id) {
        try {
            $post = Post_model::find_post_by_id($post_id);
        } catch (Exception $exception) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_INTERNAL_ERROR);
        }
        
        return $this->response_success(['post' => User_model::preparation($post, 'default')]);
    }

    public function buy_boosterpack()
    {
        // Check user is authorize
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        // TODO: task 5, покупка и открытие бустерпака
    }





    /**
     * @return object|string|void
     */
    public function get_boosterpack_info(int $bootserpack_info)
    {
        // Check user is authorize
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }


        //TODO получить содержимое бустерпака
    }
}
