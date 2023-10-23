<?php
header('Access-Control-Allow-Origin: *');
/*
 * ==========================================================
 * AJAX.PHP
 * ==========================================================
 *
 * Ajax functions. This file must be executed only via ajax. © 2020 board.support. All rights reserved.
 *
 */

if (!isset($_POST['function'])) {
    die('true');
}
global $SB_LANGUAGE;
$SB_LANGUAGE = sb_post('language');
require_once('functions.php');

if (!defined('SB_API') && sb_security() == false) {
    die(sb_json_response(new SBError('security-error', $_POST['function'])));
}

switch ($_POST['function']) {
    case 'emoji':
        die(file_get_contents(SB_PATH . '/resources/json/emoji.json'));
    case 'saved-replies':
        die(sb_json_response(sb_get_setting('saved-replies')));
    case 'save-settings':
        die(sb_json_response(sb_save_settings($_POST['settings'], $_POST['external_settings']) ));
    case 'get-settings':
        die(sb_json_response(sb_get_settings()));
    case 'get-all-settings':
        die(sb_json_response(sb_get_all_settings()));
    case 'get-front-settings':
        die(sb_json_response(sb_get_front_settings()));
    case 'get-block-setting':
        die(sb_json_response(sb_get_block_setting($_POST['value'])));
    case 'add-user':
        die(sb_json_response(sb_add_user($_POST['settings'], sb_post('settings_extra', null))));
    case 'add-user-and-login':
        die(sb_json_response(sb_add_user_and_login(sb_post('settings', null), sb_post('settings_extra', null), sb_post('login_app'))));
    case 'get-user':
        die(sb_json_response(sb_get_user($_POST['user_id'], sb_post('extra'))));
    case 'get-users':
        die(sb_json_response(sb_get_users(sb_post('sorting', ['creation_time', 'DESC']), sb_post('user_types', []), sb_post('search', ''), sb_post('pagination'))));
    case 'get-new-users':
        die(sb_json_response(sb_get_new_users($_POST['datetime'])));
    case 'get-user-extra':
        die(sb_json_response(sb_get_user_extra($_POST['user_id'])));
    case 'get-online-users':
        die(sb_json_response(sb_get_online_users(sb_post('exclude_id', -1))));
    case 'search-users':
        die(sb_json_response(sb_search_users($_POST['search'])));
    case 'get-active-user':
        die(sb_json_response(sb_get_active_user(sb_post('storage', null), sb_post('db'), sb_post('login_app'))));
    case 'get-agent':
        die(sb_json_response(sb_get_agent($_POST['agent_id'])));
    case 'delete-user':
        die(sb_json_response(sb_delete_user($_POST['user_id'])));
    case 'delete-users':
        die(sb_json_response(sb_delete_users($_POST['user_ids'])));
    case 'update-user':
        die(sb_json_response(sb_update_user($_POST['user_id'], $_POST['settings'], sb_post('settings_extra', []))));
    case 'count-users':
        die(sb_json_response(sb_count_users()));
    case 'update-user-to-lead':
        die(sb_json_response(sb_update_user_to_lead($_POST['user_id'])));
    case 'get-conversations':
        die(sb_json_response(sb_get_conversations(sb_post('pagination'), sb_post('status_code'), sb_post('routing'))));
    case 'get-agent-conversations':
        die(sb_json_response(sb_get_agent_conversations($_POST['agent_id'],sb_post('pagination'), sb_post('status_code'), sb_post('routing'))));
    case 'get-new-conversations':
        die(sb_json_response(sb_get_new_conversations($_POST['datetime'], sb_post('routing'))));
    case 'get-new-agent-conversations':
        die(sb_json_response(sb_get_new_agent_conversations($_POST['agent_id'],$_POST['datetime'], sb_post('routing'))));
    case 'get-conversation':
        die(sb_json_response(sb_get_conversation($_POST['user_id'], $_POST['conversation_id'])));
    case 'get-agent-conversation':
        die(sb_json_response(sb_get_agent_conversation($_POST['user_id'], $_POST['conversation_id'])));
    case 'search-conversations':
        die(sb_json_response(sb_search_conversations($_POST['search'], sb_post('routing'))));
    case 'search-user-conversations':
        die(sb_json_response(sb_search_user_conversations($_POST['search'], sb_post('user_id'))));
    case 'new-conversation':
        die(sb_json_response(sb_new_conversation($_POST['user_id'], sb_post('status_code'), sb_post('title', ''), sb_post('department', -1), sb_post('agent_id', -1), sb_post('routing'))));
    case 'get-user-conversations':
        die(sb_json_response(sb_get_user_conversations($_POST['user_id'], sb_post('exclude_id', -1))));
    case 'get-new-user-conversations':
        die(sb_json_response(sb_get_new_user_conversations($_POST['user_id'], $_POST['datetime'])));
    case 'update-conversation-status':
        die(sb_json_response(sb_update_conversation_status($_POST['conversation_id'], $_POST['status_code'])));
    case 'update-conversation-department':
        die(sb_json_response(sb_update_conversation_department($_POST['conversation_id'], $_POST['department'], sb_post('message', ''))));
    case 'queue':
        die(sb_json_response(sb_queue(sb_post('conversation_id'), sb_post('department'))));
    case 'update-users-last-activity':
        die(sb_json_response(sb_update_users_last_activity($_POST['user_id'], sb_post('return_user_id', -1), sb_post('check_slack'))));
    case 'is-typing':
        die(sb_json_response(sb_is_typing($_POST['user_id'], $_POST['conversation_id'])));
    case 'is-agent-typing':
        die(sb_json_response(sb_is_agent_typing($_POST['conversation_id'])));
    case 'set-typing':
        die(sb_json_response(sb_set_typing($_POST['user_id'], $_POST['conversation_id'])));
    case 'login':
        die(sb_json_response(sb_login(sb_post('email', ''), sb_post('password', ''), sb_post('user_id', ''), sb_post('token', ''))));
    case 'logout':
        die(sb_json_response(sb_logout()));
    case 'update-login':
        die(sb_json_response(sb_update_login(sb_post('profile_image', ''), sb_post('first_name', ''), sb_post('last_name', ''), sb_post('email', ''), sb_post('department', ''))));
    case 'get-new-messages':
        die(sb_json_response(sb_get_new_messages($_POST['user_id'], $_POST['conversation_id'], $_POST['datetime'])));
    case 'is-conversation-assigned':
        die(sb_json_response(sb_is_conversation_assigned($_POST['conversation_id'])));
    case 'send-message':
        die(sb_json_response(sb_send_message($_POST['user_id'], $_POST['conversation_id'], sb_post('message', ''), sb_post('attachments', []), sb_post('conversation_status_code', -1), sb_post('payload'), sb_post('queue'))));
    case 'send-bot-message':
        die(sb_json_response(sb_send_bot_message($_POST['conversation_id'], $_POST['message'], sb_post('token', -1), sb_post('language', ''), sb_post('attachments', []))));
    case 'send-slack-message':
        die(sb_json_response(sb_send_slack_message($_POST['user_id'], $_POST['full_name'], sb_post('profile_image'), sb_post('message', ''), sb_post('attachments', []), sb_post('channel', -1))));
    case 'update-message':
        die(sb_json_response(sb_update_message($_POST['user_id'], $_POST['message_id'], sb_post('message'), sb_post('attachments'), sb_post('payload'))));
    case 'delete-message':
        die(sb_json_response(sb_delete_message($_POST['user_id'], $_POST['message_id'])));
    case 'close-message':
        die(sb_json_response(sb_close_message($_POST['bot_id'], $_POST['conversation_id'])));
    case 'csv-users':
        die(sb_json_response(sb_csv_users()));
    case 'csv-conversations':
        die(sb_json_response(sb_csv_conversations(sb_post('conversation_id', -1))));
    case 'update-user-and-message':
        die(sb_json_response(sb_update_user_and_message($_POST['user_id'], sb_post('settings', []), sb_post('settings_extra', []), sb_post('message_id', ''), sb_post('message', ''), sb_post('payload'))));
    case 'get-rich-message':
        die(sb_json_response(sb_get_rich_message($_POST['name'])));
    case 'create-email':
        die(sb_json_response(sb_email_create($_POST['recipient_id'], $_POST['sender_name'], $_POST['sender_profile_image'], $_POST['message'], sb_post('attachments', []))));
    case 'send-email':
        die(sb_json_response(sb_email($_POST['recipient_id'], $_POST['message'], sb_post('attachments', []), sb_post('sender_id', -1))));
    case 'send-test-email':
        die(sb_json_response(sb_email_send_test($_POST['to'], $_POST['email_type'])));
    case 'slack-users':
        die(sb_json_response(sb_slack_users()));
    case 'archive-slack-channels':
        die(sb_json_response(sb_archive_slack_channels()));
    case 'clean-data':
        die(sb_json_response(sb_clean_data()));
    case 'user-autodata':
        die(sb_json_response(sb_user_autodata($_POST['user_id'])));
    case 'current-url':
        die(sb_json_response(sb_current_url(sb_post('user_id'), sb_post('url'))));
    case 'get-translations':
        die(sb_json_response(sb_get_translations()));
    case 'save-translations':
        die(sb_json_response(sb_save_translations($_POST['translations'])));
    case 'dialogflow-intent':
        die(sb_json_response(sb_dialogflow_intent($_POST['expressions'], $_POST['response'], sb_post('agent_language', ''))));
    case 'set-rating':
        die(sb_json_response(sb_set_rating($_POST['settings'], sb_post('payload'), sb_post('message_id'), sb_post('message'), sb_post('user_id'))));
    case 'get-rating':
        die(sb_json_response(sb_get_rating($_POST['user_id'])));
    case 'save-articles':
        die(sb_json_response(sb_save_articles($_POST['articles'])));
    case 'get-articles':
        die(sb_json_response(sb_get_articles(sb_post('id', -1), sb_post('count'), sb_post('full'))));
    case 'search-articles':
        die(sb_json_response(sb_search_articles($_POST['search'])));
    case 'installation':
        die(sb_json_response(sb_installation($_POST['details'])));
    case 'get-versions':
        die(sb_json_response(sb_get_versions()));
    case 'update':
        die(sb_json_response(sb_update()));
    case 'app-activation':
        die(sb_json_response(sb_app_activation($_POST['app_name'], $_POST['key'])));
    case 'app-get-key':
        die(sb_json_response(sb_app_get_key($_POST['app_name'])));
    case 'wp-synch':
        die(sb_json_response(sb_wp_synch()));
    case 'webhooks':
        die(sb_json_response(sb_webhooks($_POST['function_name'], $_POST['parameters'])));
    case 'system-requirements':
        die(sb_json_response(sb_system_requirements()));
    case 'get-departments':
        die(sb_json_response(sb_get_departments()));
    case 'push-notification':
        die(sb_json_response(sb_push_notification(sb_post('title'), sb_post('message'), sb_post('icon'), sb_post('interests'), sb_post('conversation_id'))));
    case 'delete-leads':
        die(sb_json_response(sb_delete_leads()));
    default:
        die('["error", "'.$DEFAULT_CHAT_SITE_NAME.' Error [ajax.php]: No functions found with name: ' . $_POST['function'] . '."]');
}

function sb_json_response($result) {
    if (sb_is_error($result)) {
        return defined('SB_API') ? sb_api_error($result) : json_encode(['error', $result->code(), $result->function_name(), $result->message()]);
    } else {
        return defined('SB_API') ? sb_api_success($result) : json_encode(sb_is_validation_error($result) ? ['validation-error', $result->code()] : ['success', $result]);
    }
}

function sb_post($key, $default = false) {
    global $_POST;
    return isset($_POST[$key]) ? ($_POST[$key] == 'false' ? false : $_POST[$key]) : $default;
}

function sb_security() {
    global $_POST;
    $security = [
        'admin' => ['delete-leads', 'system-requirements', 'save-settings', 'get-settings', 'get-all-settings', 'add-user', 'delete-user', 'delete-users', 'app-get-key', 'app-activation', 'wp-synch'],
        'agent' => ['is-agent-typing', 'close-message', 'count-users', 'update-conversation-department', 'get-users', 'get-new-users', 'get-online-users', 'search-users', 'get-conversations', 'get-new-conversations', 'search-conversations', 'csv-users', 'csv-conversations', 'send-test-email', 'slack-users', 'clean-data', 'save-translations', 'dialogflow-intent', 'get-rating', 'save-articles', 'update', 'archive-slack-channels'],
        'user' => ['search-user-conversations', 'update-login', 'update-user', 'get-user', 'get-user-extra', 'update-user-to-lead', 'new-conversation', 'get-user-conversations', 'get-new-user-conversations', 'send-slack-message', 'slack-unarchive', 'update-message', 'delete-message', 'update-user-and-message', 'get-conversation', 'get-new-messages', 'set-rating', 'create-email', 'send-email'],
        'login' => ['push-notification', 'queue', 'update-conversation-status', 'update-users-last-activity', 'is-typing', 'send-message', 'set-typing', 'user-autodata', 'saved-replies' ]
    ];
    $function = $_POST['function'];
    $user_id = sb_post('user_id', -1);
    $user = sb_get_active_user(sb_post('login', null));

    // No check
    $no_check = true;
    foreach ($security as $key => $value) {
        if (in_array($function, $security[$key])) {
            $no_check = false;
            break;
        }
    }
    if ($no_check) {
        return true;
    }

    // Login check
    if (in_array($function, $security['login']) && $user != false) {
        return true;
    }
    if ($user != false && isset($user['user_type'])) {
        $user_type = $user['user_type'];
        $current_user_id = sb_isset($user, 'id', -2);

        // User check
        if (in_array($function, $security['user']) && (sb_is_agent($user_type) || $user_id == $current_user_id)) {
            return true;
        }

        // Agent check
        if (in_array($function, $security['agent']) && sb_is_agent($user_type)) {
            return true;
        }

        // Admin check
        if (in_array($function, $security['admin']) && $user_type == 'admin') {
            return true;
        }

        return false;
    }
    return false;
}

?>