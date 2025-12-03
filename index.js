const tencentcloud = require("tencentcloud-sdk-nodejs-lighthouse");
require('dotenv').config();
const fs = require('fs');
const path = require('path');
const LighthouseClient = tencentcloud.lighthouse.v20200324.Client;
const clientConfig = {
  credential: {
    secretId: process.env.TENCENTCLOUD_SECRET_ID,
    secretKey: process.env.TENCENTCLOUD_SECRET_KEY,
  },
  region: "ap-tokyo",
  profile: {
    httpProfile: {
      endpoint: "lighthouse.tencentcloudapi.com",
    },
  },
};
const client = new LighthouseClient(clientConfig);
const params = {
  "InstanceIds": [
    process.env.TENCENTCLOUD_INSTANCE_IDS
  ]
};
client.DescribeInstances(params).then(
  (data) => {
    function formatDateChinese(value) {
      if (!value) return value;
      const m = String(value).match(/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/);
      if (m) {
        const y = m[1];
        const mm = String(m[2]).padStart(2, '0');
        const dd = String(m[3]).padStart(2, '0');
        return `${y}-${mm}-${dd}`;
      }
      const d = new Date(value);
      if (isNaN(d.getTime())) return value;
      const y = d.getFullYear();
      const mm = String(d.getMonth() + 1).padStart(2, "0");
      const dd = String(d.getDate()).padStart(2, "0"); return `${y}年${mm}月${dd}日`;
    }
    function formatDateOnly(value) {
      if (!value) return value;
      const m = String(value).match(/^(\d{4}-\d{2}-\d{2})/);
      if (m) return m[1];
      const d = new Date(value);
      if (isNaN(d.getTime())) return value;
      const y = d.getFullYear();
      const mm = String(d.getMonth() + 1).padStart(2, "0");
      const dd = String(d.getDate()).padStart(2, "0");
      return `${y}-${mm}-${dd}`;
    }

    const updateTime = formatDateOnly(new Date());

    if (data && Array.isArray(data.InstanceSet) && data.InstanceSet.length > 0) {
      const results = data.InstanceSet.map((inst) => ({
        endtime: formatDateOnly(inst.ExpiredTime) || null,
        updatatime: updateTime,
      }));
      const out = results.length === 1 ? results[0] : results;
      const outputPath = path.join(__dirname, 'endtime.json');
      try {
        fs.writeFileSync(outputPath, JSON.stringify(out, null, 2), 'utf8');
      } catch (e) {
        console.error('写入文件失败', e);
      }
      console.log(JSON.stringify(out, null, 2));
      console.log(`Saved to ${outputPath}`);
      return;
    }

    if (data && data.ExpiredTime) {
      const out = {
        endtime: formatDateOnly(data.ExpiredTime) || null,
        updatatime: updateTime,
      };
      const outputPath = path.join(__dirname, 'endtime.json');
      try {
        fs.writeFileSync(outputPath, JSON.stringify(out, null, 2), 'utf8');
      } catch (e) {
        console.error('写入文件失败', e);
      }
      console.log(JSON.stringify(out, null, 2));
      console.log(`Saved to ${outputPath}`);
      return;
    }

    console.log(data);
  },
  (err) => {
    console.error("查询失败", err);
  }
);