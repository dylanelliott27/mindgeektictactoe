<?php

class BasicWebsocketServer {
    public function __construct() {
        $this->port = 5930;
    }

    public function start_server() {
        $server_socket = stream_socket_server("tcp://127.0.0.1:5000", $errno, $errstr);

        if( !$server_socket ) {
            echo "Error starting";
            return;
        }

        while ( $client_socket = stream_socket_accept($server_socket) ) {
            $incoming_request_data = stream_socket_recvfrom($client_socket, 1500);
            $headers = $this->parse_header_string($incoming_request_data);
            $response_headers = "";

            if( array_key_exists('Upgrade', $headers) && $headers['Upgrade'] == "websocket" ) {
                // valid handshake request
                $sec_websocket_key = $headers["Sec-WebSocket-Key"];
                $response_key = base64_encode(pack('H*', sha1($sec_websocket_key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

                $response_headers = "HTTP/1.1 101 Switching Protocols\r\n";
                $response_headers .= "Upgrade: websocket\r\n";
                $response_headers .= "Connection: Upgrade\r\n";
                $response_headers .= "Sec-WebSocket-Accept: " . $response_key . "\r\n";
                $response_headers .= "\r\n";
            }
            else {
                echo "Not a WS request";
            }

            echo $response_headers;
            fwrite($client_socket, $response_headers);
            //fclose($client_socket);
        }
        fclose($server_socket);
    }

    public function parse_header_string($header_string) {
        //break string by each line (header line)
        $headers_as_array = explode("\r\n", $header_string);
        $actual_headers = [];

        foreach($headers_as_array as $header) {
            $item = explode(':', $header);

            if( ! array_key_exists(1, $item) ) {
                // incase this is the first line of headers without key:value ( get / http/1.1 )
                $actual_headers[$item[0]] = $item[0];
                continue;
            }

            $actual_headers[$item[0]] = ltrim($item[1], " ");
        }

        return $actual_headers;
    }

}

$server = new BasicWebsocketServer();

$server->start_server();