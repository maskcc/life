#define MAX_GLOBAL_DROP_COUNT 16
#define MAX_DROP_ENTITY_PER_LIST 128

typedef struct tagGlobalLootInfo
{
	int lootFlag[125];
	int nCursor;
	int nHitCount;
	DropGroup dropGroup;
}GlobalLootInfo;


typedef struct tagDropGroup
{
	ACE_UINT32 unGlobalDropID;
	ACE_UINT32 unGlobalCount;
	DropEntityInfo globalList[MAX_GLOBAL_DROP_COUNT];
	ACE_UINT32 unCount;
	DropEntityInfo list[MAX_DROP_ENTITY_PER_LIST];
}DropGroup;


typedef struct tagDropEntityInfo
{
	ACE_UINT32 unResID;
	float fRate;
}DropEntityInfo;


bool GlobalLootMgr::global_loot(GlobalLootInfo *lootInfo)
{
	bool ret = false;
	int idx = lootInfo->nCursor >> 3;
	int shift = lootInfo->nCursor & 0x7;
	if(((lootInfo->lootFlag[idx] >> shift) & 0x01) != 0)
	{
		ret = true;
	}
	lootInfo->nCursor ++;
	if(lootInfo->nCursor >= 1000)
	{
		reset(lootInfo);
	}

	return ret;
}


ACE_UINT32 GlobalLootMgr::loot(DropGroup &group, int nCount)
{
	int i;
	int c = nCount;

	GlobalLootInfo *lootInfo = NULL;

	if(group.unGlobalDropID != 0)
	{
		if(m_lootMap.find(group.unGlobalDropID, lootInfo) == 0)
		{
			c = nCount;
			while(c > 0)
			{
				if(global_loot(lootInfo))
				{
					return get_drop(lootInfo->dropGroup);
					
				}
				c --;
			}
		}
	}
	

	return get_drop(group);
}





ACE_UINT32 GlobalLootMgr::create_global_drop(float rate, ACE_UINT32 unCount, DropEntityInfo *list)
{
	if(unCount <= 0)
	{
		return 0;
	}

	int t = (int)(rate * 1000);

	if(t <= 0)
	{
		return 0;
	}
	if(t > 1000)
	{
		t = 1000;
	}

	GlobalLootInfo *info = new GlobalLootInfo();

	if(info == NULL)
	{
		return 0;
	}

	ACE_OS::memset(info, 0, sizeof(GlobalLootInfo));

	ACE_UINT32 ret = m_unCurrentID;

	if(m_lootMap.bind(ret, info) != 0)
	{
		delete info;
		return 0;
	}

	info->nHitCount = t;

	info->dropGroup.unCount = unCount;

	int i;
	for(i = 0;i < unCount;i ++)
	{
		info->dropGroup.list[i] = list[i];
	}

	reset(info);


	m_unCurrentID ++;

	return ret;
	
}


void GameUtils::parse_drop_group(DropGroup &group, char *strList, bool hasGlobal)
{
	group.unCount = 0;

	char tmp_str[256];

	int len = ACE_OS::strlen(strList);
	int sub_len = 0;

	int offset = 0;

	int c = 0;
	int global_c = 0;

	char total_str[4096];

	ACE_OS::memcpy(total_str, strList, len);
	total_str[len] = '\0';
	if(total_str[len - 1] != ';')
	{
		total_str[len] = ';';
		total_str[len + 1] = '\0';
		len ++;
	}

	while(offset < len)
	{
		if(total_str[offset + sub_len] == ';')
		{
			ACE_OS::memcpy(tmp_str, &total_str[offset], sub_len * sizeof(char));
			tmp_str[sub_len] = '\0';

			if(parse_drop_entity(&group.list[c], tmp_str) == 0)
			{
				if(group.list[c].fRate < 0.0f)
				{
					group.list[c].fRate = -group.list[c].fRate;
					if(hasGlobal)
					{
						group.globalList[global_c] = group.list[c];

						global_c ++;
					}
					else
					{
						c ++;
					}
				}
				else
				{
					c ++;
				}
			}

			offset += (sub_len + 1);
			sub_len = 0;
		}
		else
		{
			sub_len ++;
		}
	}

	group.unGlobalCount = (ACE_UINT32)global_c;
	group.unCount = (ACE_UINT32)c;
}



void GlobalLootMgr::reset(GlobalLootInfo *info)
{
	ACE_OS::memset(info->lootFlag, 0, sizeof(int) * 125);
	info->nCursor = 0;

	int c = 1000;
	int pos = 0;
	
	int tmp_c = info->nHitCount;

	if(info->nHitCount > 0)
	{
		int count = 1000 / tmp_c;

		int i = 0;
		for(i = 0;i < tmp_c;i ++)
		{
			pos = i * count + ACE_OS::rand() % count;

			int idx = pos >> 3;
			int shift = pos & 0x7;

			info->lootFlag[idx] = info->lootFlag[idx] | (1 << shift);
		}
	}
}