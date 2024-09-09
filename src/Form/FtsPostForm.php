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
        $form['field_gtin'] = [
            '#type' => 'textfield',
            '#title' => $this->t('GTIN/Código de Barras'),
            '#required' => TRUE,
        ];

        $form['field_descricao'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Descrição'),
            '#required' => TRUE,
        ];

        $form['field_marca'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Marca'),
            '#required' => TRUE,
        ];

        $form['field_volume'] = [
            '#type' => 'number',
            '#title' => $this->t('Volume'),
            '#required' => TRUE,
        ];

        $form['field_unidade'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Unidade'),
            '#required' => TRUE,
        ];

        $form['field_quantidade'] = [
            '#type' => 'number',
            '#title' => $this->t('Quantidade'),
            '#required' => TRUE,
        ];

        $form['field_preco'] = [
            '#type' => 'number',
            '#title' => $this->t('Preço'),
            '#required' => TRUE,
        ];

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
        $gtin = $form_state->getValue('field_gtin');
        $description = $form_state->getValue('field_descricao');
        $marca = $form_state->getValue('field_marca');
        $volume = $form_state->getValue('field_volume');
        $unidade = $form_state->getValue('field_unidade');
        $quantidade = $form_state->getValue('field_quantidade');
        $price = $form_state->getValue('field_preco');

// Monte o payload para a requisição POST
        $payload = [
            'products' => [
                [
                    'gtin' => $gtin,
                    'description' => $description,
                    'marca' => $marca,
                    'volume' => $volume,
                    'unidade' => $unidade,
                    'quantidade' => $quantidade,
                    'price' => $price,
                ]
            ]
        ];

// Faça a requisição POST usando Guzzle
        $client = new Client();
        try {
            $response = $client->post('http://localhost:41062/w3mleva/mleva/web/mleva-post', [
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
