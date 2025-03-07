<?php

/**
 * ����������� �������� �������, ��� ������� ��� �������.
 * ������� ������ ��� ����� �������������� ���������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCron
 */

// ��������� ��� SSH Cron
$enabled = false;

if (function_exists('set_time_limit'))
    set_time_limit(0);

if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
}
else
    $_classPath = "../../../";

include_once($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(["base", "system", "orm"]);
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// �����������
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("������ �����������!");

class CacheFilter {

    // �������
    private $debug = false;

    public function __construct() {

        // ��� ��������
        $getCategoriesAll = $this->getCategories();

        // �������� � ��������
        $getCategoriesWithProducts = (new PHPShopOrm('phpshop_products'))->getList(['DISTINCT category'], ['enabled' => "='1'", 'parent_enabled' => "='0'"], false, ['limit' => '10000']);

        // ��������� ������ � �����
        if (is_array($getCategoriesWithProducts))
            foreach ($getCategoriesWithProducts as $cat) {
                $getCategoriesWithProductsCheck[$cat['category']] = $cat['category'];
            }

        // ��������� ������ � �����
        if (is_array($getCategoriesAll))
            foreach ($getCategoriesAll as $cat) {
                $getCategoriesAllCheck[$cat['id']] = $cat;
                $getCategoriesAllCheck[$cat['id']]['sort'] = unserialize($cat['sort']);
            }

        unset($getCategoriesWithProducts);
        unset($getCategoriesAll);

        if (is_array($getCategoriesAllCheck))
            foreach ($getCategoriesAllCheck as $key => $cat) {

                // ���� ������
                if (isset($getCategoriesWithProductsCheck[$cat['id']])) {
                    $getCategoriesWithProducts[$key] = $cat;

                    // ������� �� ������� ���� ���������
                    unset($getCategoriesAll[$cat['id']]);
                }
            }

        $this->getCategoriesAll = $getCategoriesAllCheck;
        $this->createCache($getCategoriesWithProducts);

        // ����� �������� ��������� � ��������
        if (is_array($this->cache)) {
            foreach ($this->cache as $cat => $cache) {

                // ������ � ��
                $this->writeCache($cat, $cache);

                // ����� ������������ ���������
                $this->setParentCache($cache['parent_to'], $cache);
            }
        }

        //print_r($this->cache);
        echo "��� �������� � " . count($this->getCategoriesAll) . " ���������";
    }

    private function setParentCache($cat, $cache) {

        if (is_array($this->getCategoriesAll[$cat])) {

            if (is_array($cache['filter_cache']))
                foreach ($cache['filter_cache'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        if (empty($this->cache[$cat]['filter_cache'][$key][$k]))
                            $this->cache[$cat]['filter_cache'][$key][$k] = $v;
                    }
                }


            // ����������
            if (is_array($cache['products']))
                foreach ($cache['products'] as $key => $val) {
                    foreach ($val as $k => $v) {

                        if (!empty($v)) {

                            if ($this->debug)
                                echo '������� ' . $cat . ' ����� ' . $key . '-' . $k . ' ���� ' . (int) $this->cache[$cat]['products'][$key][$k] . ' ������� ' . $v . ' ����� ' . ((int) $this->cache[$cat]['products'][$key][$k] + $v) . '<br>';

                            $this->cache[$cat]['products'][$key][$k] += $v;
                        }
                    }
                }

            if (is_array($this->cache[$cat]['products']))
                foreach ($this->cache[$cat]['products'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        if (!empty($v))
                            unset($this->cache[$cat]['filter_cache'][$key][$k]);
                    }
                }

            // ������ � ��
            $this->writeCache($cat, $this->cache[$cat]);

            $this->setParentCache($this->getCategoriesAll[$cat]['parent_to'], $cache);
        }
    }

    private function getCategories() {
        $orm = new PHPShopOrm('phpshop_categories');

        return $orm->getList(['id', 'sort', 'parent_to', 'name'], ['skin_enabled' => "!='1'"]);
    }

    private function createCache($categories) {
        foreach ($categories as $category) {
            $sorts = $category['sort'];
            $total = 0;

            if (is_array($sorts) && count($sorts) > 0) {
                $cache = [];
                foreach ($sorts as $sort) {
                    $values = $this->getSortValues((int) $sort);

                    if (is_array($values)) {
                        foreach ($values as $value) {
                            $count = $this->countProducts((int) $value, (int) $sort, (int) $category['id']);
                            $total += $count;

                            if ($count === 0) {
                                $cache['filter_cache'][(int) $sort][$value] = (int) $value;
                            }

                            $cache['products'][(int) $sort][(int) $value] = $count;
                        }

                        $cache['products'][(int) $sort][(int) $value] = $count;
                    }
                }

                $this->cache[$category['id']] = $cache;
                $this->cache[$category['id']]['parent_to'] = $category['parent_to'];
                $this->cache[$category['id']]['name'] = $category['name'];
            }
        }
    }

    private function writeCache($cat, $cache) {
        $orm = new PHPShopOrm('phpshop_categories');
        $orm->debug = false;
        $orm->update(['sort_cache_new' => serialize($cache), 'sort_cache_created_at_new' => time()], ['id=' => $cat]);
    }

    private function getSortValues($sortId) {

        // ������?
        $filtr = (new PHPShopOrm('phpshop_sort_categories'))->getOne(['filtr'], ['id' => sprintf('="%s"', $sortId)])['filtr'];

        if (!empty($filtr)) {
            $orm = new PHPShopOrm('phpshop_sort');

            return array_column($orm->getList(['id'], ['category' => sprintf('="%s"', $sortId)]), 'id', 'id');
        }
    }

    private function countProducts($valueId, $sortId, $categoryId) {
        $orm = new PHPShopOrm();
        $orm->debug = false;
        $result = $orm->query(sprintf('select COUNT("id") as count from `phpshop_products` where `category`="%s" and vendor REGEXP "i%s-%si" and enabled="1" and parent_enabled="0"', $categoryId, $sortId, $valueId));

        $row = mysqli_fetch_assoc($result);

        return (int) $row['count'];
    }

}

new CacheFilter();
?>