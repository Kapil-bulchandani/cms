<?php

namespace Drupal\chatgptx\Controller;

use GuzzleHttp\Client;
use Drupal\Core\Controller\ControllerBase;


class chatgptSendData extends ControllerBase {

public function chatgptxSendDataBuild() {

  $client = new Client();
  $url = 'https://api.openai.com/v1/fine-tunes';
  $headers = array(
    'Content-Type' => 'application/json',
    //'Authorization' => 'Bearer ' . $form_state['values']['openai_api_key'],
    'Authorization' => 'Bearer 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',
    //'OpenAI-Organization' => 'org-CLUp5svhZt1pWQgxHjXV9Mqe',
  );
  $data = array(
    'data' => '{"prompt":"<Product Name>\n<Wikipedia description>\n\n###\n\n", "completion":" <engaging ad> END"}', // Add your uploaded data here
    'model' => 'text-davinci-003',
    'prompt' => 'protein recipie',
    'temperature' => 0.7,
    'max_tokens' => 1024,
    'n' => 1,
    'stop' => '.',
  );

  try {
    $response = $client->post($url, array(
      'headers' => $headers,
      'json' => $data,
    ));

    if ($response->getStatusCode() == 200) {
      drupal_set_message(t('Data uploaded to OpenAI successfully.'));
    }
    else {
      drupal_set_message(t('Error uploading data to OpenAI: @error', array('@error' => $response->getBody())), 'error');
    }
  }
  catch (Exception $e) {
    drupal_set_message(t('Error uploading data to OpenAI: @error', array('@error' => $e->getMessage())), 'error');
  }
}
} // end of controller 