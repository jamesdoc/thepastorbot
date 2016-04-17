<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require "config.php";
  require "lookup.php";
  require "twitteroauth/autoload.php";
  use Abraham\TwitterOAuth\TwitterOAuth;

  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

  $tweet_id = 0;

  $mentions = twi_mentions($connection, last_response_id());

  if (isset($mentions->errors) && $mentions->errors) {
    echo $mentions->errors[0]->message;
    return;
  }

  // Respond in order they were recieved, so reverse the array
  $mentions = array_reverse($mentions);

  foreach($mentions as $mention) {
    $tweet_id = $mention->id_str;
    $verse = find_verse($lookup, $mention->text);

    // If you can't think of something intelegent to say
    // don't say anything
    if (!$verse) continue;

    $tweet = write_tweet(
      $mention->user->screen_name,
      $mention->user->name,
      $verse[0],
      $verse[1]
    );

    send_response($connection, $tweet, $mention->id_str);

    echo "$tweet</hr>";

    // Follow the user
    if (!$mention->user->following) {
      $statuses = $connection->post(
        "friendships/create",
        ["screen_name" => $mention->user->screen_name]
      );
    }
  }

  // Log the last tweet we responded to...
  if ($tweet_id != 0) update_last_response_id($tweet_id);


  // Find a verse based on emote
  // There is a better way to do this...
  function find_verse($lookup, $tweet_body) {
    $words = str_word_count($tweet_body, 1);
    foreach($words as $word) {
      foreach($lookup as $ref) {
        if (in_array($word, $ref['keywords'])) {
          return array(
            $ref['keyword'],
            $ref['verses'][mt_rand(0, count($ref['verses'])-1)],
          );
        }
      }
    }
  }


  function write_tweet($username, $name, $feel, $verse) {
    $link = "http://esvonline.org/" . urlencode($verse);
    $name = first_name($name);
    return "@$username Hi $name, have you read $verse? It has a lot to say about $feel. $link";
  }


  // Get the first word from a username...
  function first_name($name) {
    return str_word_count($name, 1)[0];
  }


  function send_response($connection, $tweet_body, $reply_id) {
    $statuses = $connection->post(
      "statuses/update",
      [
        "status" => $tweet_body,
        "in_reply_to_status_id" => $reply_id
      ]
    );
  }

  function twi_mentions($connection, $since_id = 0) {
    $mentions = $connection->get(
      "statuses/mentions_timeline",
      [
        "since_id" => $since_id,
        "contributor_details" => true
      ]
    );
    return $mentions;
  }

  function last_response_id() {
    $file = "respond_id.txt";
    $id = fgets(fopen($file, 'r'));
    return $id;
  }

  function update_last_response_id($id) {
    $filename = "respond_id.txt";
    $file = fopen($filename, "w") or die("Unable to open file!");
    fwrite($file, $id);
    fclose($file);
  }
