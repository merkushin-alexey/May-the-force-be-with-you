<?php

use Model\Boosterpack_model;
use Model\Post_model;
use Model\Comment_model;
use Model\User_model;
use Model\Login_model;


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
        $login = (string)App::get_ci()->input->post('login');
        $password = (string)App::get_ci()->input->post('password');

        if(!empty($login)
            && !empty($password)
        ){
            Login_model::login($login, $password);
            if(User_model::is_logged())
            {
                return $this->response_success();
            }
            else $this->response_error('Username or password is incorrect');
        }
        else $this->response_error('Something went wrong');
        
    }

    public function logout()
    {
        Login_model::logout();
        redirect('/');
    }

    public function comment()
    {
        // TODO: task 2, комментирование
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $user = User_model::get_user();

        $assign_id = (int)App::get_ci()->input->post('postId');
        $text  = (string)App::get_ci()->input->post('commentText');

        if(!empty($assign_id)
            && !empty($text)
        ){
            $insert_data = array(
                'assign_id' => $assign_id,
                'text' => htmlspecialchars(trim_and_clean($text)),
                'user_id' => $user->get_id(),
                'likes' => 0
            );
            $new_comment = Comment_model::create($insert_data);
            return $this->response_success(['comment'=> $new_comment]);
        }
        else return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);

    }

    public function like_comment(int $comment_id)
    {
        // TODO: task 3, лайк комментария
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $user = User_model::get_user();

        if(!empty($comment_id))
        {
            if($user->get_likes_balance()>0)
            {
                $comment = Comment_model::get_by_id($comment_id);
                if($comment->set_likes((int)$comment->get_likes()+1))
                {
                    $user->set_likes_balance((int)$user->get_likes_balance()-1);
                    return $this->response_success(['likes'=> $comment->get_likes()]);
                }
                    
                else 
                    return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);
            }
            else return $this->response_error('Likes balance is 0');
        }
        else return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);
    }

    public function like_post(int $post_id)
    {
        // TODO: task 3, лайк поста
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $user = User_model::get_user();

        if(!empty($post_id))
        {
            if($user->get_likes_balance()>0)
            {
                $post = Post_model::get_by_id($post_id);
                if($post->set_likes((int)$post->get_likes()+1))
                {
                    $user->set_likes_balance((int)$user->get_likes_balance()-1);
                    return $this->response_success(['likes'=> $post->get_likes()]);
                }   
                else 
                    return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);
            }
            else return $this->response_error('Likes balance is zero');
        }
        else return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);
    }

    public function add_money()
    {
        // TODO: task 4, пополнение баланса
        if ( ! User_model::is_logged())
        {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $user = User_model::get_user();

        $sum = (float)App::get_ci()->input->post('sum');

        if(!empty($sum) && $sum > 0)
        {   
            if($user->add_money($sum))
            {
                //reload to get updated object
                $user->reload();
                return $this->response_success(
                    ['wallet_balance'=> $user->get_wallet_balance(),
                    'wallet_total_refilled'=> $user->get_wallet_total_refilled()]
                );
            }
            else $this->response_error('Something went wrong');
        }
        else return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NO_DATA);

        

    }

    public function get_post(int $post_id) {
        // TODO получения поста по id
        $post = Post_model::preparation(Post_model::get_by_id($post_id),'full_info');
        return $this->response_success(['post' => $post]);
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
