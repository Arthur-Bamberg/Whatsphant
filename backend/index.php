<?php

switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':
        echo '
            <a href="localhost:8081/whatsphant/auth"><h1>Auth</h1></a>
            <br>
            <a href="localhost:8081/whatsphant/users"><h1>Users</h1></a>
            <br>
            <a href="localhost:8081/whatsphant/chats"><h1>Chats</h1></a>
            <br>
            <a href="localhost:8081/whatsphant/messages"><h1>Messages</h1></a>
            <br>
        ';
        break;

    case '/whatsphant/auth':
        require_once __DIR__ . '/src/services/AuthenticationService.php';
        break;

    case '/whatsphant/users':
        require_once __DIR__ . '/src/services/UserService.php';
        break;

    case '/whatsphant/chats':
        require_once __DIR__ . '/src/services/ChatService.php';
        break;

    case '/whatsphant/messages':
        require_once __DIR__ . '/src/services/MessageService.php';
        break;

    default:
        http_response_code(404);
        exit('Not Found');
}