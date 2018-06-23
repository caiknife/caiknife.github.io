<?php
class EventsController extends AppController {
    public $uses = array();

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->getEventManager()->attach(
            function($profile){
                pr('In an anonymous function with passParams set to true.');
                pr($profile);
            
            }, 'Controller.events.index', array(
                'passParams' => true,
                'priority' => 7,
            )
        );

        $this->getEventManager()->attach(
            function($event){
                pr('In an anonymous with passParams set to true to false.');
                pr($event->data['profile']);
            }, 
            'Controller.events.index', 
            array(
                'priority' => 5,
            )
        );

        $this->getEventManager()->attach(
            'print_profile', 
            'Controller.events.index', 
            array(
                'passParams' => true,
                'priority' => 1,
            )
        );

        $this->getEventManager()->attach(
            'print_profile_with_event', 
            'Controller.events.index', 
            array(
                'priority' => 3,
            )
        );

        $this->getEventManager()->attach(
            array($this, '_profile'), 
            'Controller.events.index',
            array('priority' => -1)
        );

        $this->getEventManager()->attach(new ProfileListener());
    }

    public function index() {
        /**
         * CakeEvent构造函数接受三个参数
         * @param $name 事件名称，尽量全局唯一
         * @param $subject 被观察者对象，一般都用 $this
         * @param $data 保存参数，可以通过 $event->data 获得
         * @type {CakeEvent}
         */
        $event = new CakeEvent(
            'Controller.events.index', 
            $this, 
            array(
                'profile' => array(
                    'name' => 'caiknife',
                    'email' => 'caiknife@foxmail.com',
                ),
            )
        );
        /**
         * 进行广播，调用观察者的方法
         */
        $this->getEventManager()->dispatch($event);

        debug($event->result);
    }

    public function _profile($event) {
        pr('In an class method.');
        pr($event->data);
        $event->result[__METHOD__] = 'class method';
    }
}

function print_profile($profile) {
    pr('In an pre-defined function with passParams set to true.');
    pr($profile);
}

function print_profile_with_event($event) {
    pr('In an pre-defined function with passParams set to false.');
    pr($event->data);
}

class ProfileListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'Controller.events.index' => array(
                'callable' => 'profile', 
                'priority' => -2
            ),
        );
    }

    public function profile($event) {
        pr('In an class listener.');
        pr($event->data);
        // 被观察者是我们的 EventsController 对象的一个实例
        pr(get_class($event->subject()));
        // 可以像平常一样调用 Controller 的各种方法
        pr($event->subject()->request->params);
        $event->result[__METHOD__] = 'class method';
    }
}