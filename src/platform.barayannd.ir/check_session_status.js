// تابعی که نتیجه بررسی توکن سشن ذخیره شده را از سرور میگیرد
const checkSession = async (token) => {
    const URL = "/platform.barayannd.ir/ends/check_session_status.php"

    const res = await fetch(URL, {
        method: "POST",
        headers: {
            "Content-Type":"application/json"
        },
        body: JSON.stringify({session_token: token})
    })

    if(!res.ok){
        throw new Error("Invalid Response");
    }

    try{
        return await res.json()
    }
    catch (e){
        console.error(e)
        return null
    }
}

// تابعی که به دنبال کوکی مربوط به سشن می گردد
const searchCookie = () => {
    const cookies = document.cookie.split("; ")
    const ckIndex = cookies.findIndex(ck => ck.includes("BarayanndSessionToken"))

    if(ckIndex === -1){
        return null
    }

    const ckItem = cookies[ckIndex]
    const cookie = ckItem.split("=")[1]

    return cookie ?? null
}

// تولید یک تایم استمپ مربوط به زمان گذشته (جهت منقضی کردن کوکی های دلخواه)
const generateExpiredTimestamp = () => {
    const now = new Date()
    now.setTime(now.getTime() - 1000000)
    return now.toUTCString()
}

// این تابع با توجه به خروجی بازگشتی سرور، عمل مورد نیاز را انجام میدهد
const serverSideCheck = async () => {
    const sessionCookie = searchCookie()
    const checkResult = await checkSession(sessionCookie)

    if(!checkResult.success){
        console.warn(checkResult.status)

        if(checkResult.action === "clear"){
            document.cookie = `BarayanndSessionToken=null;path=/;expires=${generateExpiredTimestamp()}`
            setTimeout(()=> {
                location.reload()
            }, 200)
        }
    }
    else {
        console.info("Authentication Successful")
        if(checkResult.action === "reload"){
            setTimeout(()=> {
                location.reload()
            }, 200)
        }
        else if(checkResult.action === "set & reload"){

            const expiry = checkResult?.data?.expiry
            const token = checkResult?.data?.token

            if(expiry && token){
                const expiryStr = new Date(expiry).toUTCString()
                document.cookie = `BarayanndSessionToken=${token};path=/;expires=${expiryStr}`
                setTimeout(()=> {
                    location.reload()
                }, 200)
            }
            else{
                console.error("Failed to fetch session data")
            }
        }
    }
}

serverSideCheck()