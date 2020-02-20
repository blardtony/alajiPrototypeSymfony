<?php
namespace App\Tests;

use App\Tests\AcceptanceTester;

class UserCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToLoginSuccess(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jsimonet.alaji@gmail.com');
        $I->fillField('password', 'julien');
        $I->click('Se connecter');
        $I->see('Examinateur : Julien Simonet');
    }

    public function tryToLoginWrongPassword(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jsimonet.alaji@gmail.com');
        $I->fillField('password', '123456');
        $I->click('Se connecter');
        $I->see('Invalid credentials.');
    }

    public function tryToLoginWrongEmail(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'jsimmonet.alaji@gmail.com');
        $I->fillField('password', 'julien');
        $I->click('Se connecter');
        $I->see('Email could not be found.');
    }
}
