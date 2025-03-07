<?php 

namespace PaySystem;

/**
 * ������ ���������� � �������� IntellectMoney.
 */
class IntegrationMethod {
    /**
     * ����� �� ���������.
     */
    const P2P = 'P2P';

    /**
     * ����� ������ �� ���������.
     */
    const DefaultMethod = 'Default'; 

    /**
     * ���������� ��� ��������� �������� � ���� �������.
     * @return array
     * ������ ���� ��������� ��������.
     */
    public static function values()
    {
        return [
            IntegrationMethod::DefaultMethod, 
            IntegrationMethod::P2P,
        ];
    }
}