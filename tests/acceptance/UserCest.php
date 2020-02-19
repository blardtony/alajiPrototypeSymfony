<?php
namespace App\Tests;

use App\Tests\AcceptanceTester;

class UserCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToLoginJulien(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jsimonet.alaji@gmail.com');
        $I->fillField('password', 'julien');
        $I->click('Se connecter');
        $I->see('Examinateur : Julien Simonet');
    }
}
