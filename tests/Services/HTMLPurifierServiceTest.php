<?php

namespace Tests;

use Railroad\Railforums\Services\HTMLPurifierService;

class HTMLPurifierServiceTest extends TestCase
{
    /**
     * @var HTMLPurifierService
     */
    private $classBeingTested;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(HTMLPurifierService::class);
    }

    public function test_get_threads_sorted_paginated()
    {
        $html = "
<h1>In qua quid est boni praeter summam voluptatem, et eam sempiternam?</h1>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Beatum, inquit. Quis negat? <i>Nos commodius agimus.</i> <mark>Si longus, levis;</mark> At multis se probavit. <b>Duo Reges: constructio interrete.</b> <a href='http://loripsum.net/' target='_blank'>Memini me adesse P.</a> </p>

<blockquote cite='http://loripsum.net'>
	Neque enim in aliqua parte, sed in perpetuitate temporis vita beata dici solet, nec appellatur omnino vita, nisi confecta atque absoluta, nec potest quisquam alias beatus esse, alias miser;
</blockquote>


<pre>
Id et fieri posse et saepe esse factum et ad voluptates
percipiendas maxime pertinere.

Nam ista vestra: Si gravis, brevis;
</pre>


<dl>
	<dt><dfn>Quonam modo?</dfn></dt>
	<dd>Ergo ita: non posse honeste vivi, nisi honeste vivatur?</dd>
	<dt><dfn>An eiusdem modi?</dfn></dt>
	<dd>Quae hic rei publicae vulnera inponebat, eadem ille sanabat.</dd>
	<dt><dfn>Memini vero, inquam;</dfn></dt>
	<dd>Ex ea difficultate illae fallaciloquae, ut ait Accius, malitiae natae sunt.</dd>
	<dt><dfn>Quid vero?</dfn></dt>
	<dd>Et quidem iure fortasse, sed tamen non gravissimum est testimonium multitudinis.</dd>
	<dt><dfn>Equidem e Cn.</dfn></dt>
	<dd>In qua si nihil est praeter rationem, sit in una virtute finis bonorum;</dd>
</dl>


<p><i>Efficiens dici potest.</i> Tamen a proposito, inquam, aberramus. Omnes enim iucundum motum, quo sensus hilaretur. Sequitur disserendi ratio cognitioque naturae; </p>

<p>Illa tamen simplicia, vestra versuta. <i>Nos vero, inquit ille;</i> <i>Ne discipulum abducam, times.</i> Si quicquam extra virtutem habeatur in bonis. Teneo, inquit, finem illi videri nihil dolere. Quis Aristidem non mortuum diligit? Ut aliquid scire se gaudeant? Quaerimus enim finem bonorum. </p>

<ul>
	<li>Nam illud vehementer repugnat, eundem beatum esse et multis malis oppressum.</li>
	<li>Cum audissem Antiochum, Brute, ut solebam, cum M.</li>
	<li>His singulis copiose responderi solet, sed quae perspicua sunt longa esse non debent.</li>
	<li>Sed tu istuc dixti bene Latine, parum plane.</li>
	<li>Deinde qui fit, ut ego nesciam, sciant omnes, quicumque Epicurei esse voluerunt?</li>
	<li>Vitae autem degendae ratio maxime quidem illis placuit quieta.</li>
</ul>


<ol>
	<li>Velut ego nunc moveor.</li>
	<li>Sed tempus est, si videtur, et recta quidem ad me.</li>
	<li>Ad corpus diceres pertinere-, sed ea, quae dixi, ad corpusne refers?</li>
</ol>


<p>Non igitur bene. At enim hic etiam dolore. Nos cum te, M. Nam ante Aristippus, et ille melius. Sin aliud quid voles, postea. Ecce aliud simile dissimile. Bestiarum vero nullum iudicium puto. </p>

<p>Pollicetur certe. Videsne quam sit magna dissensio? Inde igitur, inquit, ordiendum est. Istic sum, inquit. Paria sunt igitur. Maximus dolor, inquit, brevis est. Primum Theophrasti, Strato, physicum se voluit; <a href='http://loripsum.net/' target='_blank'>Negat esse eam, inquit, propter se expetendam.</a> Pugnant Stoici cum Peripateticis. </p>

";
        $this->classBeingTested->clean($html);
    }
}