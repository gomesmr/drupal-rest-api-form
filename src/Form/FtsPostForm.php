<?php

namespace Drupal\fts_post_request\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Exception;
use GuzzleHttp\Client;

class FtsPostForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'fts_post_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Primeiro conjunto de campos para GTIN 01 e Preço 01
        $form['group1'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('GTIN 01 e Preço 01'),
        ];

        $form['group1']['field_gtin_1'] = [
            '#type' => 'textfield',
            '#title' => $this->t('GTIN 01'),
            '#required' => TRUE,
        ];

        $form['group1']['field_preco_1'] = [
            '#type' => 'number',
            '#title' => $this->t('Preço 01'),
            '#required' => TRUE,
        ];

        // Segundo conjunto de campos para GTIN 02 e Preço 02
        $form['group2'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('GTIN 02 e Preço 02'),
        ];

        $form['group2']['field_gtin_2'] = [
            '#type' => 'textfield',
            '#title' => $this->t('GTIN 02'),
            '#required' => TRUE,
        ];

        $form['group2']['field_preco_2'] = [
            '#type' => 'number',
            '#title' => $this->t('Preço 02'),
            '#required' => TRUE,
        ];

        // Botão de envio do formulário
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Pegue os valores do formulário
        $gtin1 = $form_state->getValue('field_gtin_1');
        $price1 = $form_state->getValue('field_preco_1');
        $gtin2 = $form_state->getValue('field_gtin_2');
        $price2 = $form_state->getValue('field_preco_2');

        // Monte o payload para a requisição POST
        $payload = [
            'products' => [
                [
                    'gtin' => $gtin1,
                    'price' => $price1,
                ],
                [
                    'gtin' => $gtin2,
                    'price' => $price2,
                ]
            ]
        ];

        // Faça a requisição POST usando Guzzle
        $client = new Client();
        try {
            $response = $client->post('http://host.docker.internal:41062/w3mleva/mleva/web/mleva-post', [
                'json' => $payload,
            ]);

            // Verifica a resposta
            $status_code = $response->getStatusCode();
            if ($status_code == 200) {
                $this->messenger()->addMessage($this->t('Dados enviados com sucesso!'));
            } else {
                $this->messenger()->addError($this->t('Erro ao enviar os dados. Status code: @code', ['@code' => $status_code]));
            }
        } catch (Exception $e) {
            $this->messenger()->addError($this->t('Erro ao conectar com o serviço: @message', ['@message' => $e->getMessage()]));
        }
    }
}
