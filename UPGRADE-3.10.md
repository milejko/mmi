CUSTOM SESSION HANDLERS CONFIG CHANGE:

was:   
session->handler = 'user';  
session->path = '/Namespace/ClassName';

current:  
session->handler = '/Namespace/ClassName';
session->path = 'optional path - i.e. redis address';

Redis example:  
session->handler = '/Mmi/Session/RedisHandler';  
session->path = 'tcp://localhost:6379/1';