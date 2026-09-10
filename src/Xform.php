<?php

declare(strict_types=1);

namespace Icmbio\Xform;

/**
 * Biblioteca PHP para processamento XForm
 * 
 * @method array<string> shortGetNodeset() Retorna nomes dos campos abreviados
 * @method array<string> shortGetAttachments() Retorna anexos com nomes abreviados
 * @method array<string> shortGetGeopoints() Retorna geopoints com nomes abreviados
 * @method array<string> shortGetGeotraces() Retorna geotraces com nomes abreviados  
 * @method array<string> shortGetGeoshapes() Retorna geoshapes com nomes abreviados
 * @method array<string> shortGetGroupRepeats() Retorna grupos repetitivos com nomes abreviados
 * @method array<string> shortGetGroupRepeatsWithUuid() Retorna grupos com UUID abreviados
 * @method array<string> shortGetNamesXform() Retorna nomes do Xform abreviados
 */
class Xform
{
    use Concerns\XmlDocument,
        Concerns\SurveyInstance,
        Concerns\SurveyGeo,
        Concerns\SurveyAttachment,
        Concerns\SurveyGroup,
        Concerns\SurveyGroupRepeat;

    protected ?string $group_repeat = null;
    protected ?string $group_repeat_plural = null;

    public function __construct(string $content, ?string $group_repeat = null, ?string $group_repeat_plural = null)
    {
        $this->boot($content);
        $this->group_repeat = $group_repeat;
        $this->group_repeat_plural = $group_repeat_plural;
    }
}
