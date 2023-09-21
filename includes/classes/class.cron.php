<?php class HubCron {
    public function __construct() {
        $this->get_members();
    }

    public function get_members() {
        if ( ! is_admin() ) {
            require_once( ABSPATH . 'wp-admin/includes/post.php' );
        }

        $request = wp_remote_get( 'https://thisiscrowd.com/wp-json/crowd/v1/teams' );
        $body    = wp_remote_retrieve_body( $request );

        if ( ! empty( $body ) ) {
            $data = json_decode( $body );

            //$test = array_chunk($data,3);

            //echo '<pre>';print_r($test);echo '</pre>';

            echo '<br>';echo '<br>';echo '<br>';echo '<br>';

            foreach ($data as $member) {
                $member_position = $member->designation;
                $member_linkedin = $member->linkedin;
                $user            = get_user_by( 'login', $member->name );

                //echo '<pre>';print_r($user);echo '</pre>';

                if(!$user) {
                    $split_name = explode( ' ', $member->name );

                    echo '<pre>';print_r($member->name);echo '</pre>';
                    echo '<br>';

                    $user_data = [
                        'user_login'    => $member->name,
                        'user_nicename' => $member->name,
                        'nickname'      => $member->name,
                        'display_name'  => $member->name,
                        'first_name'    => ! empty( $split_name ) && sizeof( $split_name ) == 2 ? $split_name[0] : '',
                        'last_name'     => ! empty( $split_name ) && sizeof( $split_name ) == 2 ? $split_name[1] : '',
                        'user_pass'     => wp_generate_password()
                    ];
                    /*$user_id = wp_insert_user([

                    ]);*/

                    /*if( ! is_wp_error( $user_id ) ){
                        return true;
                    }*/
                } else {
                    $user_id = $user->ID;
                }

                //$this->save_user_image('',$member->image);
            }
        }
    }

    public function save_user_image($post_id, $image_url) {
        $upload_dir = wp_upload_dir();

        $image_data = file_get_contents( $image_url );

        $filename = basename( $image_url );

        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        }
        else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents( $file, $image_data );

        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name( $filename ),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attachment_check = new Wp_Query( $attachment );
        if($attachment_check->have_posts()) {
            $attach_id = $attachment_check->post->ID;
        } else {
            $attach_id = wp_insert_attachment( $attachment, $file );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }

        set_post_thumbnail( $post_id, $attach_id );
    }
}

new HubCron();
