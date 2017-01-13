extern crate philipshue;
extern crate schedule_recv;
extern crate time;

use std::env;
use std::time::Duration;
use philipshue::hue::LightCommand;
use philipshue::bridge::Bridge;

fn rgb_to_hsv(r: u8, g: u8, b: u8) -> (u16, u8, u8) {
    let r = r as f64 / 255f64;
    let g = g as f64 / 255f64;
    let b = b as f64 / 255f64;
    let max = r.max(g.max(b));
    let min = r.min(g.min(b));

    if max == min {
        (0, 0, (max * 255.) as u8)
    } else {
        let d = max - min;
        let s = d / max;
        let h = if max == r {
            (g - b) / d + (if g < b { 6f64 } else { 0f64 })
        } else if max == g {
            (b - r) / d + 2f64
        } else {
            (r - g) / d + 4f64
        };
        ((65535. * h / 6.) as u16, (s * 255.) as u8, (max * 255.) as u8)
    }
}

fn blink(r: u8, g: u8, b: u8) {
    let bridge = Bridge::new(env::var("huebridge").unwrap(), env::var("hueuser").unwrap());
    let group = 1;
    let (hue, sat, bri) = rgb_to_hsv(r, g, b);
    let cmd_blink = LightCommand {
        hue: Some(hue),
        sat: Some(sat),
        bri: Some(bri),
        alert: Some("lselect".to_owned()),
        .. LightCommand::default().on()
    };

    match bridge.set_group_state(group, &cmd_blink) {
        Ok(resps) => {
            for resp in resps.into_iter() {
                println!("{:?}", resp)
            }
        }
        Err(e) => println!("Error occured when trying to send request:\n\t{}", e),
    }
    
    std::thread::sleep(Duration::from_millis(15_000));
    let cmd_off = LightCommand::default().off().with_alert("none".to_owned());
    
    match bridge.set_group_state(group, &cmd_off) {
        Ok(resps) => {
            for resp in resps.into_iter() {
                println!("{:?}", resp)
            }
        }
        Err(e) => println!("Error occured when trying to send request:\n\t{}", e),
    }
}

fn log() {
    println!("Tick: {}", time::now().strftime("%Y-%m-%d %H:%M:%S.%f").unwrap());
}

fn main() {
    log();
    let tick = schedule_recv::periodic(Duration::from_millis(30_000));
    loop {
        // match on events happened to determine color        
        match true {
            true => blink(128,128,0),
            _ =>    println!("This should never happen!"),
        }
        tick.recv().unwrap();
        log();
    }
}