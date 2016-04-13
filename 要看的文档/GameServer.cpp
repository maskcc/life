133734276831
515287574175

广东省广州市天河区天河南一路100号302  王娜   15800002632 

上海 上海市 闸北区 大宁路街道 江场西路299弄中铁中环时代广场6号楼803 ，200070

发送到付件，一个小盒子，最好惊天能来，不能来请回复

你记得发这个地址：广州市萝岗区景新二街四号，梁工，18664627374

1.typedef ACE_Singleton<ServiceConfigMgr, ACE_Thread_Mutex> SERVICE_CONFIG_MGR_INSTANCE;   //加载服务器配置文件的单例
2.ServiceInstanceInfo
typedef struct tagServiceInstanceInfo
{
	char name[MAX_SERVICE_INFO_NAME + 1];
	ACE_UINT32 unID;
	ConfigAddress lanAdr;
	ConfigAddress wlanAdr;
	ConfigAddress publicAdr;
	bool isServiceMgr;
}ServiceInstanceInfo;

3.typedef ACE_Singleton<CommunicationMgr, ACE_Thread_Mutex> COMMUNICATION_MGR_INSTANCE;
4.ServerCommandEndPoint
5.ITEM_RES_MGR_INSTANCE
6.LOGGER_CLIENT_INSTANCE

7.消息的传递
typedef struct tagMessageInfo
{
  ACE_Message_Block *msg;
  PacketHead *p_head;  //??
  MsgInfoHead *i_head;
  ServiceHead *s_head;
  MessageHead *m_head;  //??
  void *body;
  int nBodySize;
}MessageInfo;

typedef struct tagPacketHead
{
  ACE_UINT32 unType;  //??
  ACE_UINT32 unCtrlCode;  //?
}PacketHead;
typedef struct tagMsgInfoHead
{
  ACE_UINT32 unSig;
  ServiceHead s_head;
}MsgInfoHead;
typedef struct tagServiceHead
{
  ObjAdr desAdr;   //目的适配器
  ObjAdr srcAdr;   //源适配器
}ServiceHead;
typedef struct tagObjAdr
{
  ACE_UINT32 unServiceID;
  ACE_UINT32 unAdapterID;
  ACE_UINT32 unObjID;
  ACE_UINT32 unTaskID;
}ObjAdr;

typedef struct tagMessageHead
{
  ACE_UINT32 unType;
  ACE_UINT32 unPacketNO;
}MessageHead;

m_unCurrentSig = m_unLoginID;  //m_unLoginID是有一个登录的+1
msg.m_head->unPacketNO = unPacketNo;  //记录包的数量


class ServiceMsgPeer
{
public:
  enum
  {
    STATUS_NORMAL = 0,
    STATUS_PAUSE
  };
  ServiceMsgPeer(ACE_UINT32 unAdapterID, ACE_UINT32 unObjID);
  ~ServiceMsgPeer();
  int send_msg(MessageInfo &msgInfo);
  int pause(ACE_UINT32 unServiceID);
  int resume(ACE_UINT32 unServiceID);
  int ack(ACE_UINT32 unPacketNo);
  void reset();
private:
  ACE_UINT32 m_unAdapterID;
  ACE_UINT32 m_unObjID;
  int m_currentStatus;
  ACE_UINT32 m_unCurrentServiceID;
  int cache_msg(MessageInfo &msgInfo);
  void remove_cache_msg_from_tail();
  MSG_PEER_CACHE_LIST m_cache_list;
};

typedef ACE_DLList<CacheMsgNode> MSG_PEER_CACHE_LIST;
typedef struct tagCacheMsgNode
{
  ACE_UINT32 unPacketNo;
  ObjAdr *des_adr;
  ACE_Message_Block *msg;
}CacheMsgNode;


enum
{
  ADAPTER_GAME_SERVER_MGR = 1,
  ADAPTER_MAP_MGR,
  ADAPTER_GATEWAY_MGR,
  ADAPTER_LOGIN_MGR,
  ADAPTER_DB_HOME_MGR,
  ADAPTER_DB_LOGIN_MGR,
  ADAPTER_DB_USER_MGR,
  ADAPTER_DB_FRIEND_INFO_MGR,
  ADAPTER_PHP_PROXY_MGR,
  ADAPTER_USER_INFO_MGR,
  ADAPTER_ADMIN_COMMAND_MGR,
  ADAPTER_LOGGER_SERVER_MGR,
  ADAPTER_TEST_MGR
};


                  service_define_list:      service_instance:
Game Server           1                           1                   Game Server
Daemon                2
Login Server          3
Gateway               4
DB Server             5                           1                   DB Server
Map Server            6
Php Proxy Server      7
User Info Server      8


//这些命令的作用
#define MSG_SERVICE_OBJ_UNREACHABLE         MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x000c)
#define MSG_SERVICE_OBJ_MSG_REACHED         MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x000d)
#define MSG_SERVICE_QUERY_OBJ_LOCATION        MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x000e)
#define MSG_SERVICE_ANSWER_OBJ_LOCATION       MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x000f)
#define MSG_SERVICE_WANNA_CREATE_SERVICE_OBJ    MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x0010)
#define MSG_SERVICE_WANNA_DESTROY_SERVICE_OBJ   MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x0011)
#define MSG_SERVICE_CREATE_SERVICE_OBJ        MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x0012)
#define MSG_SERVICE_DESTROY_SERVICE_OBJ       MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x0013)
#define MSG_SERVICE_CREATE_SERVICE_OBJ_RESULT   MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x0014)
#define MSG_SERVICE_DESTROY_SERVICE_OBJ_RESULT    MAKE_MSG_TYPE(SERVICE_PROTOCOL_GROUP, 0x0015)


enum
{
  SERVICE_MESSAGE_TYPE_CTRL = 0,
  SERVICE_MESSAGE_TYPE_TASK,
  SERVICE_MESSAGE_TYPE_TIMER,
  SERVICE_MESSAGE_TYPE_OBJ_LOCATION,
  SERVICE_MESSAGE_TYPE_DB_REQUEST,
  SERVICE_MESSAGE_TYPE_DB_RESPONSE,
  SERVICE_MESSAGE_TYPE_DB_REQUEST_ACK,
  SERVICE_MESSAGE_TYPE_SYSTEM,
  SERVICE_MESSAGE_TYPE_USER,
};

#define SERVICE_MESSAGE_CTRL_NONE 0x00000000
#define SERVICE_MESSAGE_CTRL_NEED_QUERY_LOCATION 0x00000001
#define SERVICE_MESSAGE_CTRL_TO_INTERNET 0x40000000
#define SERVICE_MESSAGE_CTRL_FROM_INTERNET 0x80000000

#define MAKE_MSG_TYPE(g, t) ((g << 16) | t)`