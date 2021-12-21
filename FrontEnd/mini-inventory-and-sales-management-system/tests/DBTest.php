<?php

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase {
    //Unit Test 1
    public function testAmountOfVat() {
        $test1 = new Function\Functions;
        $result = $test1->getVatAmount(1000,5);
        $this->assertEquals(50, $result, "% Impuesto Calculado Correctamente");
    }

    //Unit Test 2
    public function testIsPhoneNumber() {
        $test1 = new Function\Functions;
        $result = $test1->is_phone_number(79408866);
        $this->assertEquals(79408866, $result, "Verificación de Número de Teléfono Correcta");
    }

    //Unit Test 3
    public function testIsRealName() {
        $test1 = new Function\Functions;
        $result = $test1->is_real_name("Santiago");
        $this->assertEquals("Santiago", $result, "Verificación de Nombre Valido Correcta");
    }

    //Unit Test 4
    public function testIsFakeName() {
        $test1 = new Function\Functions;
        $result = $test1->is_real_name("Pedro900");
        $this->assertEquals("", $result, "Verificación de Nombre Invalido Correcta");
    }

    //Unit Test 5
    public function testIsEmail() {
        $test1 = new Function\Functions;
        $result = $test1->is_email("santiavctf@gmail.com");
        $this->assertEquals("santiavctf@gmail.com", $result, "Verificación de Correo Valido Correcta");
    }

    //Unit Test 6
    public function testIsNotEmail() {
        $test1 = new Function\Functions;
        $result = $test1->is_email("santiavctf");
        $this->assertEquals("", $result, "Verificación de Correo Invalido Correcta");
    }

    //Unit Test 7
    public function testEncryptPassword() {
        $test1 = new Function\Functions;
        $result = $test1->hash_pass("SoyUnaClave123");
        $this->assertNotEquals("SoyUnaClave123", $result, "Verificación de Cifrado de Contraseña Correcta");
    }

    //Unit Test 8
    public function testGetFileErrorMessage() {
        $test1 = new Function\Functions;
        $result = $test1->getFileError(2);
        $this->assertEquals("Exceeds Max File Size in html", $result, "Verificación de Código de Error Correcto");
    }

    //Unit Test 9
    public function testDiscountAmount() {
        $test1 = new Function\Functions;
        $result = $test1->getDiscountAmount(1000,8.5);
        $this->assertEquals(85, $result, "% Descuento Calculado Correctamente");
    }

    //Unit Test 10
    public function testIsUrl() {
        $test1 = new Function\Functions;
        $result = $test1->is_url("http://www.example.com/index.html");
        $this->assertEquals("http://www.example.com/index.html", $result, "Verificación de URL CorrectA");
    }

    //Unit Test 11
    public function testGetSecondFileErrorMessage() {
        $test1 = new Function\Functions;
        $result = $test1->getFileError(4);
        $this->assertEquals("No file uploaded", $result, "Verificación de Código de Error Correcto");
    }

}