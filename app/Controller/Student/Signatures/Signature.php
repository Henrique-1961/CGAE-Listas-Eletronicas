<?php

namespace App\Controller\Student\Signatures;

use App\Controller\Student\Page;
use App\Controller\Common\Alert;
use App\Model\Entity\Aluno as Aluno;
use App\Model\Entity\Listas\VaiVolta as VaiVolta;
use App\Model\Entity\Listas\Pernoite as Pernoite;
use App\Model\Entity\Listas\Saida as Saida;
use Exception;

/**
 * Controlador da página de assinatura (aluno)
 */
class Signature extends Page
{
    /**
     * Retorna a view da página de assinatura
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista correspondente a assinatura
     * @param int $id ID da assinatura no banco
     * @param string $message Texto da mensagem de alerta
     * @param bool $success Indica se a mensagem corresponde a um processo bem ou mal sucedido
     * @return string View renderizada
     */
    public static function getSignature($list, $id, $message = null, $success = false)
    {
        // CONFIGURA A NAVBAR
        parent::setActiveModule("assinaturas");

        // OBTÉM OS DADOS DA ASSINATURA
        switch ($list)
        {
            case "vai_volta":
                $arr = (array) VaiVolta::getSignatureById($id) ?? throw new Exception("not found", 404);
                $type = "Vai e volta";
                break;
                
            case "saida":
                $arr = (array) Saida::getSignatureById($id) ?? throw new Exception("not found", 404);
                $type = "Saída";
                break;
                
            case "pernoite":
                $arr = (array) Pernoite::getSignatureById($id) ?? throw new Exception("not found", 404);
                $type = "Pernoite";
                break;
                
            default:
                throw new Exception("not found", 404);
        }

        // RENDERIZA A VIEW
        $content = parent::render("/signature/index", [
            "status" => is_null($message) ? "" : "<hr>".($success ? Alert::getSuccess($message) : Alert::getError($message)),
            "list" => $list,
            "id" => $id,
            "dados" => self::createJsObj($arr, $type),
            "edit" => self::getEdit($list, $id)
        ]);

        return parent::getPage("Minhas Assinaturas", $content);
    }

    /**
     * Processa as ações da página
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista correspondete a assinatura
     * @param int $id ID da assinatura no banco
     * @return string View renderizada
     */
    public static function setSignature($request, $list, $id)
    {
        // OBTÉM AS VARIÁVIES DE POST
        $postVars = $request->getPostVars();

        // VERIFICA SE ALGUMA AÇÃO FOI SOLICITADA
        switch ($postVars['acao'])
        {
            case "encerrar":
                return self::endSignature($list, $id);

            default:
                return self::getSignature($request, $list, $id);
        }
    }

    /**
     * Encerra uma assinatura
     * @param Request $request Objeto de requisição
     * @param string $list Nome da lista a qual pertence a assinatura
     * @param int $id ID da assinatura no banco
     * @return string View renderizada
     */
    private static function endSignature($list, $id)
    {
        // INICIALIZA A SESSÃO
        \App\Session\Login::init();
        
        // OBTÉM A DATA E HORA ATUAL
        date_default_timezone_set("America/Sao_Paulo");
        $dataAtual = date("Y-m-d", time());
        $horaAtual = date("H:i:s", time() + 60);

        // ATUALIZA A ASSINATURA
        switch ($list)
        {
            case "vai_volta":
                VaiVolta::atualizarAssinaturas("id = ".$id, [
                    "hora_chegada" => $horaAtual,
                    "ativa" => false
                ]);
                break;

            case "saida":
                Saida::atualizarAssinaturas("id = ".$id, [
                    "data_chegada" => $dataAtual,
                    "hora_chegada" => $horaAtual,
                    "ativa" => false
                ]);
                break;

            case "pernoite":
                Pernoite::atualizarAssinaturas("id = ".$id, [
                    "data_chegada" =>$dataAtual,
                    "hora_chegada" => $horaAtual,
                    "ativa" => false
                ]);
                break;
        }

        return self::getSignature($list, $id, "Assinatura encerrada", true);
    }

    private static function createJsObj($arr, $type)
    {
        $keys = array_keys($arr);
        $values = array_values($arr);
        $res = "{Lista: '".$type."', ";
        $res .= "renderEdit: ".($arr['pai'] == NULL ? "true" : "false").", ";

        for ($i = 0; $i < count($keys); $i++)
        {
            switch ($keys[$i])
            {
                case "id":
                    continue 2;

                case "pai":
                    continue 2;

                case "aluno":
                    $values[$i] = Aluno::getAlunoById($values[$i])->nome;
                    break;

                case "nomeResponsavel":
                    $keys[$i] = "nomeDoResponsável";
                    break;

                case "endereco":
                    $keys[$i] = "endereço";
                    break;

                case "dataSaida":
                    $keys[$i] = "dataDeSaída";
                    break;

                case "dataChegada":
                    $keys[$i] = "dataDeChegada";
                    break;

                case "horaSaida":
                    $keys[$i] = "horárioDeSaída";
                    break;

                case "horaChegada":
                    $keys[$i] = "horárioDeChegada";
                    break;
            }

            if (str_contains($keys[$i], "data"))
            {
                $values[$i] = explode("-", $values[$i]);
                $values[$i] = join("/", array_reverse($values[$i]));
            }
            
            $keys[$i] = strtolower(preg_replace(["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"], ["_$1", "_$1_$2"], lcfirst($keys[$i])));

            $res .= ($keys[$i] == "ativa" ? $keys[$i] : ucfirst($keys[$i])).": ".($keys[$i] == "ativa" ? ($values[$i] ? "true" : "false") : "'".$values[$i]."'").", ";
        }

        return substr($res, 0, -2)."}";
    }
    
    private static function createEditJsObj($arr, $list)
    {
        $keys = array_keys($arr);
        $values = array_values($arr);
        $res = "{type: '".$list."', ";

        for ($i = 0; $i < count($keys); $i++)
        {
            switch ($keys[$i])
            {
                case "id":
                    break;

                case "endereco":
                    break;

                case "destino":
                    break;

                case "dataSaida":
                    break;

                case "dataChegada":
                    break;

                case "horaSaida":
                    break;

                case "horaChegada":
                    break;

                default: 
                    continue 2;
            }

            if (str_contains($keys[$i], "data"))
            {
                $values[$i] = explode("-", $values[$i]);
                $values[$i] = join("/", array_reverse($values[$i]));
            }
            
            $keys[$i] = strtolower(preg_replace(["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"], ["_$1", "_$1_$2"], lcfirst($keys[$i])));

            $res .= ($keys[$i] == "id" ? "id" : ucfirst($keys[$i])).": "."'".$values[$i]."', ";
        }

        return substr($res, 0, -2)."}";
    }

    private static function getEdit($list, $id)
    {
        $arr = [];
        $fatherId = $id;

        switch ($list)
        {
            case "vai_volta":
                while (True)
                {
                    $aux = VaiVolta::getSignatureByFather($fatherId);

                    if (is_null($aux)) break;

                    $fatherId = $aux->id;
                    $arr[] = $aux;
                }
                
                break;

            case "saida":
                while (True)
                {
                    $aux = Saida::getSignatureByFather($fatherId);

                    if (is_null($aux)) break;

                    $fatherId = $aux->id;
                    $arr[] = $aux;
                }

                break;

            case "pernoite":
                while (True)
                {
                    $aux = Pernoite::getSignatureByFather($fatherId);

                    if (is_null($aux)) break;

                    $fatherId = $aux->id;
                    $arr[] = $aux;
                }

                break;
        }

        if (!count($arr)) return "[]";

        $res = "[";

        for ($i = 0; $i < count($arr); $i++)
        {
            $res .= self::createEditJsObj((array) $arr[$i], $list).", ";
        }

        return substr($res, 0, -2)."]";
    }
}

?>