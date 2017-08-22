<?php
namespace common\widgets;

use newhouse\components\CreateUrl;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use Yii;

class GoLinkPager extends LinkPager {

    public $pageSizeOptions = [
        'id' => 'perpage',
        'class' => 'form-control2',
        'style' => [
            'display' => 'inline-block',
            'width' => '50px',
            'line-height' => '20px',
            'margin-top' => '0px',
        ],
    ];
    public $pageSizeList = [10, 15, 20, 25, 30, 50,100];
    // 是否包含跳转功能跳转 默认false
    public $go = true;
    public $model = '';
    public $page = 0;

    protected function renderPageButtons() {
        $this->options = ['class'=>'pagination','style'=>['margin'=>'0px']];//设置分页样式类
        if(!empty(($this->page))){
            $this->maxButtonCount = 5;
            $this->pagination->setPage($this->page - 1,true);
        }
        $pageCount = $this->pagination->getPageCount();
        $buttons = [];
        $currentPage = $this->pagination->getPage();
        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
        }
        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }
        // internal pages
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i == $currentPage);
        }
        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }
        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        //自定义每页显示条数
        $pageSizeList = [];
        foreach ($this->pageSizeList as $value) {
            $pageSizeList[$value] = $value;
        }
        $customPage = Html::dropDownList($this->pagination->pageSizeParam, $this->pagination->getPageSize(), $pageSizeList, $this->pageSizeOptions);

        // go
        if ($this->go) {
            $goPage = $currentPage + 1;
            $goHtml = <<<goHtml
                <div class="" style="float: left; color: #999; margin-left: 10px; font-size: 12px;">
                    <span class="text">共 {$pageCount} 页, {$this->pagination->totalCount} 条</span>
                    <span class="text">到第</span>
                    <input class="form-control" id='gopage' type="text" value="{$goPage}" min="1" max="{$pageCount}" aria-label="页码输入框" style="height:28px;width:35px;display:inline">
                    <span class="text">页</span>
                   <span class="btn btn-default go-page" role="button" style="padding:3px;height:28px;" tabindex="0" >GO</span>
                    &nbsp;&nbsp每页显示{$customPage}
                </div>  
goHtml;
            $buttons[] = $goHtml;
            $pageLink = $this->pagination->createUrl(0, 5, true);
            $goJs = <<<goJs
                 $('.go-page').click(function(){
                    pager();
                });
                
                $("#perpage").change(function (){ 
                    pager();
                });

                function pager(){
                    customPage = $("#perpage").val();
                    goPage = $("#gopage").val();
                    pageLink = "{$pageLink}";
                    pageLink = pageLink.replace("page=1", "page="+goPage);
                    pageLink = pageLink.replace("per-page=5", "per-page="+customPage); 
                    if (goPage >= 1 && goPage <= {$pageCount}) {
                        window.location.href=pageLink;
                    } else {
                        $("#gopage").focus();
                    }
                }
goJs;
            $this->view->registerJs($goJs);
        }
        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }


    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = ['class' => $class === '' ? null : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);

            return Html::tag('li', Html::tag('span', $label), $options);
        }

        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;
        if(!empty($this->model)){
            return Html::tag('li', Html::a($label, $this->createUrl($page)), $options);
        }else{
            return Html::tag('li', Html::a($label, $this->pagination->createUrl($page)), $options);
        }

    }


    public function createUrl($page, $pageSize = null, $absolute = false)
    {
        $page = (int) $page;
        $pageSize = (int) $pageSize;
        if (($params = $this->pagination->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }
        if ($page > 0 || $page == 0 && $this->pagination->forcePageParam) {
            $params[$this->pagination->pageParam] = $page + 1;
        } else {
            unset($params[$this->pagination->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->pagination->getPageSize();
        }
        if ($pageSize != $this->pagination->defaultPageSize) {
            $params[$this->pagination->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pagination->pageSizeParam]);
        }
        $params[0] = $this->pagination->route === null ? Yii::$app->controller->getRoute() : $this->route;
        $urlManager = $this->pagination->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;
        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        } else {
            return CreateUrl::createSearchUrl($this->model,array('page'=>$params['page']));
            //return $urlManager->createUrl($params);
        }
    }


}